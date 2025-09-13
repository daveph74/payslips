<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: white;
            color: black;
        }

        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }

        /* Header Section */
        .header {
            margin-bottom: 30px;
        }

        .company-logo {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(45deg, #ff6b35, #000);
            border-radius: 8px;
            margin-right: 15px;
            position: relative;
        }

        .logo-icon::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            bottom: 8px;
            border: 2px solid white;
            border-radius: 4px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: black;
        }

        .payslip-title {
            font-size: 36px;
            font-weight: bold;
            color: black;
            margin: 20px 0;
        }

        /* Employee Information */
        .employee-details {
            margin-bottom: 30px;
        }

        .employee-details div {
            margin-bottom: 8px;
            font-size: 14px;
        }

        .employee-details strong {
            font-weight: bold;
        }

        /* Section Titles */
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: black;
            margin: 25px 0 15px 0;
        }

        /* Tables */
        .payslip-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .payslip-table th,
        .payslip-table td {
            border: 1px solid black;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        .payslip-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .payslip-table .amount-header {
            text-align: right;
        }

        .payslip-table .amount-cell {
            text-align: right;
            font-weight: bold;
        }

        /* Earnings Section */
        .earnings-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .earnings-table th,
        .earnings-table td {
            border: 1px solid black;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        .earnings-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .earnings-table .amount-header {
            text-align: right;
        }

        .earnings-table .amount-cell {
            text-align: right;
            font-weight: bold;
        }

        /* Deductions Section */
        .deductions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .deductions-table th,
        .deductions-table td {
            border: 1px solid black;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        .deductions-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .deductions-table .amount-header {
            text-align: right;
        }

        .deductions-table .amount-cell {
            text-align: right;
            font-weight: bold;
        }

        /* Take Home Pay Section */
        .take-home-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .take-home-table th,
        .take-home-table td {
            border: 1px solid black;
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }

        .take-home-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .take-home-table .amount-header {
            text-align: right;
        }

        .take-home-table .amount-cell {
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }

        /* Currency formatting */
        .currency {
            font-weight: bold;
        }

        /* Hide empty values */
        .empty-value {
            color: #ccc;
        }
    </style>
</head>

<body>
    <div class="payslip-container">
        <!-- Header Section -->
        <div class="header">
            <div class="company-logo">
                <div class="logo-icon"></div>
                <div class="company-name">OutWorx</div>
            </div>
            <div class="payslip-title">PAYSLIP</div>
        </div>

        <!-- Employee Information -->
        <div class="employee-details">
            <div><strong>Employee Name:</strong> {{ $payroll->employee->name }}</div>
            <div><strong>Employee ID:</strong> {{ $payroll->employee->employee_id }}</div>
            <div><strong>Department:</strong> {{ $payroll->employee->department }}</div>
            <div><strong>Pay Period:</strong> {{ $payroll->period }}</div>
            <div><strong>Pay date:</strong> {{ $payroll->pay_date->format('M d, Y') }}</div>
        </div>

        <!-- Earnings Section -->
        <div class="section-title">Earnings</div>
        <table class="earnings-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount-header">Amount in PHP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Pay period salary</td>
                    <td class="amount-cell">₱{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                @if($payroll->allowances > 0)
                <tr>
                    <td>Parking/Transpo Allowance</td>
                    <td class="amount-cell">₱{{ number_format($payroll->allowances, 2) }}</td>
                </tr>
                @endif
                @if($payroll->overtime > 0)
                <tr>
                    <td>Holiday Pay</td>
                    <td class="amount-cell">₱{{ number_format($payroll->overtime, 2) }}</td>
                </tr>
                @endif
                @if($payroll->bonus > 0)
                <tr>
                    <td>Bonus</td>
                    <td class="amount-cell">₱{{ number_format($payroll->bonus, 2) }}</td>
                </tr>
                @endif
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td>Total Earnings</td>
                    <td class="amount-cell">₱{{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime + $payroll->bonus, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Deductions Section -->
        <div class="section-title">Deductions</div>
        <table class="deductions-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount-header">Amount in PHP</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Social Security System</td>
                    <td class="amount-cell">
                        @if($payroll->deductions > 0)
                        ₱{{ number_format($payroll->deductions * 0.11, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>PhilHealth</td>
                    <td class="amount-cell">
                        @if($payroll->deductions > 0)
                        ₱{{ number_format($payroll->deductions * 0.05, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Pag-ibig</td>
                    <td class="amount-cell">
                        @if($payroll->deductions > 0)
                        ₱{{ number_format($payroll->deductions * 0.02, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Withholding Tax</td>
                    <td class="amount-cell">
                        @if($payroll->tax > 0)
                        ₱{{ number_format($payroll->tax, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Loans</td>
                    <td class="amount-cell">
                        @if($payroll->deductions > 0)
                        ₱{{ number_format($payroll->deductions * 0.3, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Unpaid Absences, Tardiness</td>
                    <td class="amount-cell">
                        @if($payroll->deductions > 0)
                        ₱{{ number_format($payroll->deductions * 0.2, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Others Authorized Deductions</td>
                    <td class="amount-cell">
                        @if($payroll->deductions > 0)
                        ₱{{ number_format($payroll->deductions * 0.32, 2) }}
                        @else
                        <span class="empty-value">₱0.00</span>
                        @endif
                    </td>
                </tr>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td>Total Deductions</td>
                    <td class="amount-cell">₱{{ number_format($payroll->deductions + $payroll->tax, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Take Home Pay Section -->
        <div class="section-title">Take Home Pay for the Period</div>
        <table class="take-home-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="amount-header">Amount in PHP</th>
                </tr>
            </thead>
            <tbody>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td>Take Home Pay for the Period</td>
                    <td class="amount-cell">₱{{ number_format($payroll->net_pay, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>