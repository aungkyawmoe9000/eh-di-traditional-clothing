<?php

namespace Tests\Feature\Admin;

use App\Models\DailyReport;
use App\Models\User;
use App\Models\Sale;
use App\Models\Expense;
use App\Livewire\Admin\DailyReportManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;

class DailyReportManagerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_it_rolls_forward_stale_open_register_to_today(): void
    {
        // Mock "today" to be 2026-03-09
        Carbon::setTestNow(Carbon::parse('2026-03-09'));

        // Create a stale open register from Aug 27, 2025
        $staleReport = DailyReport::create([
            'date' => '2025-08-27',
            'opening_balance' => 150.00,
            'total_cash_sales' => 0.00,
            'total_expenses' => 0.00,
            'expected_closing_balance' => 150.00,
            'status' => 'open',
        ]);

        // Verify the stale report is in the DB with its past date
        $this->assertDatabaseHas('daily_reports', [
            'id' => $staleReport->id,
            'date' => '2025-08-27',
            'status' => 'open',
        ]);

        // Load the DailyReportManager Livewire component
        Livewire::actingAs($this->user)
            ->test(DailyReportManager::class)
            ->assertSet('date', '2026-03-09');

        // Verify the database has updated the report's date to today
        $this->assertDatabaseHas('daily_reports', [
            'id' => $staleReport->id,
            'date' => '2026-03-09',
            'status' => 'open',
        ]);

        // Clean up Carbon mocking
        Carbon::setTestNow();
    }

    public function test_it_captures_todays_sales_after_rolling_forward(): void
    {
        // Mock "today" to be 2026-03-09
        Carbon::setTestNow(Carbon::parse('2026-03-09'));

        // Create a stale open register from Aug 27, 2025
        $staleReport = DailyReport::create([
            'date' => '2025-08-27',
            'opening_balance' => 150.00,
            'total_cash_sales' => 0.00,
            'total_expenses' => 0.00,
            'expected_closing_balance' => 150.00,
            'status' => 'open',
        ]);

        // Create a sale today (2026-03-09) and an expense today
        Sale::create([
            'user_id' => $this->user->id,
            'voucher_no' => 'INV-TEST001',
            'total_amount' => 100.00,
            'net_amount' => 100.00,
            'payment_method' => 'Cash',
            'sale_date' => '2026-03-09',
        ]);

        Expense::create([
            'user_id' => $this->user->id,
            'expense_date' => '2026-03-09',
            'amount' => 30.00,
            'category' => 'Supplies',
            'description' => 'Test expense',
        ]);

        // Load the DailyReportManager Livewire component
        Livewire::actingAs($this->user)
            ->test(DailyReportManager::class)
            ->assertSet('total_cash_sales', 100.00)
            ->assertSet('total_expenses', 30.00)
            ->assertSet('expected_closing_balance', 220.00); // 150 + 100 - 30

        // Clean up Carbon mocking
        Carbon::setTestNow();
    }
}
