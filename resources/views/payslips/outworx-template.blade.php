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
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 50%;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: right;
            padding-top: 10px;
        }

        .company-logo {
            margin-bottom: 10px;
        }

        .logo-icon img {
            height: 60px;
        }

        .payslip-title {
            font-size: 36px;
            font-weight: bold;
            color: black;
            margin: 10px 0;
            text-align: center;
        }

        /* Top bar holding employee details on the left and title on the right */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* Employee Information */
        .employee-details {
            margin-bottom: 20px;
        }

        .employee-details div {
            margin-bottom: 6px;
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
            margin: 15px 0 7px 0;
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
            padding: 10px;
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
            margin-bottom: 15px;
        }

        .earnings-table th,
        .earnings-table td {
            border: 1px solid black;
            padding: 10px;
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
            margin-bottom: 15px;
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

        /* DomPDF specific optimizations */
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .payslip-container {
                max-width: none;
                margin: 0;
            }

            .header {
                page-break-inside: avoid;
            }

            .earnings-table,
            .deductions-table,
            .take-home-table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    <div class="payslip-container">
        <!-- Title centered at top -->
        <div class="payslip-title" style="margin-top:0">OutWorx PAYSLIP</div>

        <!-- Employee Information below title -->
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
                    <td class="amount-cell">{{ number_format($payroll->basic_salary, 2) }}</td>
                </tr>
                @if($payroll->allowances > 0)
                <tr>
                    <td>Parking/Transpo Allowance</td>
                    <td class="amount-cell">{{ number_format($payroll->allowances, 2) }}</td>
                </tr>
                @endif
                @if($payroll->overtime > 0)
                <tr>
                    <td>Overtime Pay</td>
                    <td class="amount-cell">{{ number_format($payroll->overtime, 2) }}</td>
                </tr>
                @endif
                @if($payroll->bonus > 0)
                <tr>
                    <td>Holiday Pay</td>
                    <td class="amount-cell">{{ number_format($payroll->bonus, 2) }}</td>
                </tr>
                @endif
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td>Total Earnings</td>
                    <td class="amount-cell">{{ number_format($payroll->basic_salary + $payroll->allowances + $payroll->overtime + $payroll->bonus, 2) }}</td>
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
                        @if($payroll->social_security_system > 0)
                        {{ number_format($payroll->social_security_system, 2) }}
                        @else
                        <span class="empty-value">0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>PhilHealth</td>
                    <td class="amount-cell">
                        @if($payroll->philhealth > 0)
                        {{ number_format($payroll->philhealth, 2) }}
                        @else
                        <span class="empty-value">0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Pag-ibig</td>
                    <td class="amount-cell">
                        @if($payroll->pag_ibig > 0)
                        {{ number_format($payroll->pag_ibig, 2) }}
                        @else
                        <span class="empty-value">0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Withholding Tax</td>
                    <td class="amount-cell">
                        @if($payroll->withholding_tax > 0)
                        {{ number_format($payroll->withholding_tax, 2) }}
                        @else
                        <span class="empty-value">0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Unpaid Absences, Tardiness</td>
                    <td class="amount-cell">
                        @if($payroll->unpaid_absences_tardiness > 0)
                        {{ number_format($payroll->unpaid_absences_tardiness, 2) }}
                        @else
                        <span class="empty-value">0.00</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>Others Authorized Deductions</td>
                    <td class="amount-cell">
                        @if($payroll->others_authorized_deductions > 0)
                        {{ number_format($payroll->others_authorized_deductions, 2) }}
                        @else
                        <span class="empty-value">0.00</span>
                        @endif
                    </td>
                </tr>
                <tr style="background-color: #f8f9fa; font-weight: bold;">
                    <td>Total Deductions</td>
                    <td class="amount-cell">{{ number_format($payroll->deductions + $payroll->tax, 2) }}</td>
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
                    <td class="amount-cell">{{ number_format($payroll->net_pay, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>

</html>