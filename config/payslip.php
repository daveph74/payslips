<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payslip Template Configuration
    |--------------------------------------------------------------------------
    |
    | This option controls which payslip template to use for PDF generation.
    | Available templates:
    | - 'outworx-template' - OutWorx company style (clean, professional)
    | - 'template' - Default modern style with gradients
    |
    */
    'template' => env('PAYSLIP_TEMPLATE', 'outworx-template'),

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | Company details for payslip generation
    |
    */
    'company' => [
        'name' => env('PAYSLIP_COMPANY_NAME', 'OutWorx'),
        'logo' => env('PAYSLIP_COMPANY_LOGO', null),
        'address' => env('PAYSLIP_COMPANY_ADDRESS', ''),
        'phone' => env('PAYSLIP_COMPANY_PHONE', ''),
        'email' => env('PAYSLIP_COMPANY_EMAIL', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency Settings
    |--------------------------------------------------------------------------
    |
    | Currency configuration for payslip display
    |
    */
    'currency' => [
        'symbol' => env('PAYSLIP_CURRENCY_SYMBOL', '₱'),
        'code' => env('PAYSLIP_CURRENCY_CODE', 'PHP'),
        'position' => env('PAYSLIP_CURRENCY_POSITION', 'before'), // before or after
    ],

    /*
    |--------------------------------------------------------------------------
    | Deduction Categories
    |--------------------------------------------------------------------------
    |
    | Standard deduction categories for Philippine payroll
    |
    */
    'deductions' => [
        'sss' => [
            'name' => 'Social Security System',
            'rate' => 0.11, // 11% of basic salary
        ],
        'philhealth' => [
            'name' => 'PhilHealth',
            'rate' => 0.05, // 5% of basic salary
        ],
        'pagibig' => [
            'name' => 'Pag-ibig',
            'rate' => 0.02, // 2% of basic salary
        ],
        'withholding_tax' => [
            'name' => 'Withholding Tax',
            'rate' => 0.0, // Calculated separately
        ],
        'loans' => [
            'name' => 'Loans',
            'rate' => 0.0, // Variable amount
        ],
        'absences' => [
            'name' => 'Unpaid Absences, Tardiness',
            'rate' => 0.0, // Variable amount
        ],
        'others' => [
            'name' => 'Others Authorized Deductions',
            'rate' => 0.0, // Variable amount
        ],
    ],
];
