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
            background-color: #f5f5f5;
        }

        .payslip-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 50%;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: center;
        }

        .company-logo {
            margin-bottom: 10px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            border: 1px solid #ddd;
        }

        .logo-icon img {
            width: 100%;
            height: 100%;
            border-radius: 12px;
        }

        .header-content {
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .content {
            padding: 30px;
        }

        .employee-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .info-section h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 16px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .info-label {
            font-weight: 600;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .salary-breakdown {
            margin-top: 30px;
        }

        .salary-breakdown h3 {
            margin: 0 0 20px 0;
            color: #333;
            font-size: 18px;
            text-align: center;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .salary-table th,
        .salary-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .salary-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .salary-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .amount {
            text-align: right;
            font-weight: 600;
        }

        .positive {
            color: #28a745;
        }

        .negative {
            color: #dc3545;
        }

        .net-pay {
            background-color: #e3f2fd;
            font-weight: bold;
            font-size: 16px;
        }

        .net-pay .amount {
            color: #1976d2;
            font-size: 18px;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
        }

        .period-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e3f2fd;
            border-radius: 6px;
        }

        .period-info h2 {
            margin: 0;
            color: #1976d2;
            font-size: 20px;
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
        }
    </style>
</head>

<body>
    <div class="payslip-container">
        <div class="header">
            <div class="header-left">
                <div class="company-logo">
                    <div class="logo-icon">
                        @if(file_exists(public_path('OutWorx.jpg')))
                        <img src="{{ asset('OutWorx.jpg') }}" alt="OutWorx Logo">
                        @else
                        <div style="width: 100%; height: 100%; background: #ff6b35; border-radius: 12px; text-align: center; line-height: 80px; color: white; font-weight: bold; font-size: 12px;">OW</div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="header-content">
                    <h1>PAYSLIP</h1>
                    <p>Salary Statement</p>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="period-info">
                <h2>Pay Period: {{ $payroll->period }}</h2>
                <p>Pay Date: {{ $payroll->pay_date->format('F d, Y') }}</p>
            </div>

            <div class="employee-info">
                <div class="info-section">
                    <h3>Employee Information</h3>
                    <div class="info-item">
                        <span class="info-label">Employee ID:</span>
                        <span class="info-value">{{ $payroll->employee->employee_id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Name:</span>
                        <span class="info-value">{{ $payroll->employee->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Department:</span>
                        <span class="info-value">{{ $payroll->employee->department }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Position:</span>
                        <span class="info-value">{{ $payroll->employee->position ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="info-section">
                    <h3>Payment Details</h3>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value" style="color: {{ $payroll->status === 'generated' ? '#28a745' : '#ffc107' }}; font-weight: bold;">
                            {{ ucfirst($payroll->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Generated:</span>
                        <span class="info-value">{{ $payroll->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <div class="salary-breakdown">
                <h3>Salary Breakdown</h3>
                <table class="salary-table">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="amount">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="amount positive">${{ number_format($payroll->basic_salary, 2) }}</td>
                        </tr>
                        @if($payroll->allowances > 0)
                        <tr>
                            <td>Allowances</td>
                            <td class="amount positive">${{ number_format($payroll->allowances, 2) }}</td>
                        </tr>
                        @endif
                        @if($payroll->overtime > 0)
                        <tr>
                            <td>Overtime</td>
                            <td class="amount positive">${{ number_format($payroll->overtime, 2) }}</td>
                        </tr>
                        @endif
                        @if($payroll->bonus > 0)
                        <tr>
                            <td>Holiday Pay</td>
                            <td class="amount positive">${{ number_format($payroll->bonus, 2) }}</td>
                        </tr>
                        @endif
                        @if($payroll->deductions > 0)
                        <tr>
                            <td>Deductions</td>
                            <td class="amount negative">-${{ number_format($payroll->deductions, 2) }}</td>
                        </tr>
                        @endif
                        @if($payroll->tax > 0)
                        <tr>
                            <td>Tax</td>
                            <td class="amount negative">-${{ number_format($payroll->tax, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="net-pay">
                            <td><strong>Net Pay</strong></td>
                            <td class="amount"><strong>${{ number_format($payroll->net_pay, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer">
            <p>This is a computer-generated payslip. No signature required.</p>
            <p>Generated on {{ now()->format('F d, Y \a\t H:i:s') }}</p>
        </div>
    </div>
</body>

</html>