<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'opening_balance',
        'total_cash_sales',
        'total_expenses',
        'expected_closing_balance',
        'actual_closing_balance',
        'discrepancy',
        'status',
        'closed_by',
    ];

    /**
     * Get the user who closed the daily report.
     */
    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
