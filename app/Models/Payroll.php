<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'period',
        'basic_salary',
        'allowances',
        'overtime',
        'bonus',
        'deductions',
        'tax',
        'net_pay',
        'pay_date',
        'status',
        // New detailed fields
        'total_earnings',
        'social_security_system',
        'philhealth',
        'pag_ibig',
        'withholding_tax',
        'loans',
        'unpaid_absences_tardiness',
        'others_authorized_deductions',
        'total_deductions',
        'extra_month',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'overtime' => 'decimal:2',
        'bonus' => 'decimal:2',
        'deductions' => 'decimal:2',
        'tax' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'pay_date' => 'date',
        // New detailed fields
        'total_earnings' => 'decimal:2',
        'social_security_system' => 'decimal:2',
        'philhealth' => 'decimal:2',
        'pag_ibig' => 'decimal:2',
        'withholding_tax' => 'decimal:2',
        'loans' => 'decimal:2',
        'unpaid_absences_tardiness' => 'decimal:2',
        'others_authorized_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'extra_month' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
