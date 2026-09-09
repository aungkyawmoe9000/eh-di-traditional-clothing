<?php

namespace App\Livewire\Admin;

use App\Models\DailyReport;
use App\Models\Sale;
use App\Models\Expense;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.app')]
class DailyReportManager extends Component
{
    use WithPagination;

    // Public properties as requested
    public $reportId;
    public $date;
    public $opening_balance = 0.00;
    public $total_cash_sales = 0.00;
    public $total_expenses = 0.00;
    public $expected_closing_balance = 0.00;
    public $actual_closing_balance;
    public $discrepancy = 0.00;
    public $status = 'open';

    public function mount()
    {
        $this->loadOrCreateActiveReport();
    }

    /**
     * Load an existing open report, or create a new one based on the latest record.
     */
    public function loadOrCreateActiveReport()
    {
        // Try to find the latest open report
        $report = DailyReport::where('status', 'open')->orderBy('date', 'desc')->first();

        if ($report) {
            // If an 'open' register exists but its date is older than today, update its date to today()
            if (Carbon::parse($report->date)->lt(Carbon::today())) {
                $report->update([
                    'date' => Carbon::today()->toDateString(),
                ]);
            }
        } else {
            // Find the most recent closed report to inherit values from
            $latestReport = DailyReport::orderBy('date', 'desc')->first();

            if ($latestReport) {
                $nextDate = Carbon::parse($latestReport->date)->addDay()->toDateString();
                $openingBalance = $latestReport->actual_closing_balance ?? $latestReport->expected_closing_balance ?? 0.00;
            } else {
                // If there are absolutely no reports in the system, default to today's date with a starting cash drawer balance of 100.00
                $nextDate = Carbon::today()->toDateString();
                $openingBalance = 100.00;
            }

            $report = DailyReport::create([
                'date' => $nextDate,
                'opening_balance' => $openingBalance,
                'total_cash_sales' => 0.00,
                'total_expenses' => 0.00,
                'expected_closing_balance' => $openingBalance,
                'status' => 'open',
            ]);
        }

        $this->loadReport($report);
    }

    /**
     * Set the component properties based on the loaded DailyReport.
     */
    public function loadReport(DailyReport $report)
    {
        $this->reportId = $report->id;
        $this->date = $report->date;
        $this->opening_balance = (float) $report->opening_balance;
        $this->status = $report->status;

        if ($this->status === 'open') {
            // For the open register, dynamically calculate the latest cash sales and expenses from DB
            $this->total_cash_sales = (float) Sale::whereDate('sale_date', $this->date)
                ->where('payment_method', 'Cash')
                ->sum('net_amount');

            $this->total_expenses = (float) Expense::whereDate('expense_date', $this->date)
                ->sum('amount');

            $this->expected_closing_balance = $this->opening_balance + $this->total_cash_sales - $this->total_expenses;
            
            if ($this->actual_closing_balance !== null && $this->actual_closing_balance !== '') {
                $this->discrepancy = (float)$this->actual_closing_balance - $this->expected_closing_balance;
            } else {
                $this->discrepancy = null;
            }
        } else {
            // If the report is already closed, use the stored values
            $this->total_cash_sales = (float) $report->total_cash_sales;
            $this->total_expenses = (float) $report->total_expenses;
            $this->expected_closing_balance = (float) $report->expected_closing_balance;
            $this->actual_closing_balance = (float) $report->actual_closing_balance;
            $this->discrepancy = (float) $report->discrepancy;
        }
    }

    /**
     * Reactively compute discrepancy as the user inputs the actual closing balance.
     */
    public function updatedActualClosingBalance($value)
    {
        if ($value === '' || $value === null) {
            $this->discrepancy = null;
            return;
        }

        if (is_numeric($value)) {
            $this->discrepancy = (float)$value - (float)$this->expected_closing_balance;
        } else {
            $this->discrepancy = null;
        }
    }

    /**
     * Close the current register, calculate final discrepancy, and create tomorrow's record.
     */
    public function closeRegister()
    {
        $this->validate([
            'actual_closing_balance' => 'required|numeric|min:0',
        ]);

        $report = DailyReport::findOrFail($this->reportId);

        if ($report->status === 'closed') {
            session()->flash('error', 'This report has already been closed.');
            return;
        }

        // Recalculate totals immediately before saving to ensure accuracy
        $this->total_cash_sales = (float) Sale::whereDate('sale_date', $this->date)
            ->where('payment_method', 'Cash')
            ->sum('net_amount');

        $this->total_expenses = (float) Expense::whereDate('expense_date', $this->date)
            ->sum('amount');

        $this->expected_closing_balance = $this->opening_balance + $this->total_cash_sales - $this->total_expenses;
        $this->discrepancy = (float) $this->actual_closing_balance - $this->expected_closing_balance;

        $report->update([
            'total_cash_sales' => $this->total_cash_sales,
            'total_expenses' => $this->total_expenses,
            'expected_closing_balance' => $this->expected_closing_balance,
            'actual_closing_balance' => $this->actual_closing_balance,
            'discrepancy' => $this->discrepancy,
            'status' => 'closed',
            'closed_by' => auth()->id(),
        ]);

        // Create tomorrow's record starting with the actual closing balance from today
        $tomorrowDate = Carbon::parse($report->date)->addDay()->toDateString();
        
        $tomorrowReport = DailyReport::where('date', $tomorrowDate)->first();

        if (!$tomorrowReport) {
            DailyReport::create([
                'date' => $tomorrowDate,
                'opening_balance' => $this->actual_closing_balance,
                'total_cash_sales' => 0.00,
                'total_expenses' => 0.00,
                'expected_closing_balance' => $this->actual_closing_balance,
                'status' => 'open',
            ]);
        }

        session()->flash('success', "Register closed successfully! New register opened for tomorrow ($tomorrowDate).");

        $this->reset('actual_closing_balance');

        // Reload the next/active daily report
        $this->loadOrCreateActiveReport();
    }

    public function render()
    {
        $history = DailyReport::where('status', 'closed')
            ->with('closedByUser')
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('livewire.admin.daily-report-manager', [
            'history' => $history,
        ]);
    }
}
