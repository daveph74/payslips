<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payroll Records</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .header-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 0;
            margin-bottom: 30px;
        }

        .header-section h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .header-section p {
            margin: 10px 0 0 0;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .stats-cards {
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .stat-card .number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-card .label {
            color: #666;
            font-size: 0.9rem;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .table-header h3 {
            margin: 0;
            color: #333;
        }

        .table {
            margin: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-generated {
            background-color: #d4edda;
            color: #155724;
        }

        .status-sent {
            background-color: #cce5ff;
            color: #004085;
        }

        .btn-action {
            padding: 6px 12px;
            margin: 2px;
            border-radius: 4px;
            font-size: 0.8rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-download {
            background-color: #28a745;
            color: white;
        }

        .btn-download:hover {
            background-color: #218838;
            color: white;
        }

        .btn-generate {
            background-color: #007bff;
            color: white;
        }

        .btn-generate:hover {
            background-color: #0056b3;
            color: white;
        }

        .btn-preview {
            background-color: #17a2b8;
            color: white;
        }

        .btn-preview:hover {
            background-color: #138496;
            color: white;
        }

        .batch-actions {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .pagination {
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="header-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-money-bill-wave"></i> Payroll Management</h1>
                    <p>Manage employee payroll records and generate payslips</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('payroll.upload') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-upload"></i> Upload New Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row stats-cards">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon text-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="number">{{ $payrolls->total() }}</div>
                    <div class="label">Total Records</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon text-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="number">{{ $payrolls->where('status', 'pending')->count() }}</div>
                    <div class="label">Pending</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon text-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="number">{{ $payrolls->where('status', 'generated')->count() }}</div>
                    <div class="label">Generated</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="icon text-info">
                        <i class="fas fa-paper-plane"></i>
                    </div>
                    <div class="number">{{ $payrolls->where('status', 'sent')->count() }}</div>
                    <div class="label">Sent</div>
                </div>
            </div>
        </div>

        <!-- Batch Actions -->
        <div class="batch-actions">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5><i class="fas fa-tasks"></i> Batch Actions</h5>
                    <p class="text-muted mb-0">Select records and perform bulk operations</p>
                </div>
                <div class="col-md-6 text-end">
                    <button class="btn btn-primary" onclick="generateSelected()">
                        <i class="fas fa-file-pdf"></i> Generate Selected Payslips
                    </button>
                    <button class="btn btn-success" onclick="generateAll()">
                        <i class="fas fa-download"></i> Generate All Payslips
                    </button>
                </div>
            </div>
        </div>

        <!-- Payroll Records Table -->
        <div class="table-container">
            <div class="table-header">
                <h3><i class="fas fa-table"></i> Payroll Records</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                            </th>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Period</th>
                            <th>Basic Salary</th>
                            <th>Net Pay</th>
                            <th>Status</th>
                            <th>Pay Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payrolls as $payroll)
                        <tr>
                            <td>
                                <input type="checkbox" class="payroll-checkbox" value="{{ $payroll->id }}">
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $payroll->employee->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $payroll->employee->employee_id }}</small>
                                </div>
                            </td>
                            <td>{{ $payroll->employee->department }}</td>
                            <td>{{ $payroll->period }}</td>
                            <td>${{ number_format($payroll->basic_salary, 2) }}</td>
                            <td><strong>${{ number_format($payroll->net_pay, 2) }}</strong></td>
                            <td>
                                <span class="status-badge status-{{ $payroll->status }}">
                                    {{ ucfirst($payroll->status) }}
                                </span>
                            </td>
                            <td>{{ $payroll->pay_date->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('payslips.preview', $payroll) }}" class="btn-action btn-preview me-2" target="_blank">
                                    <i class="fas fa-eye"></i> Preview
                                </a>
                                @if($payroll->status === 'generated')
                                <a href="{{ route('payslips.download', $payroll) }}" class="btn-action btn-download">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                @else
                                <a href="{{ route('payslips.generate', $payroll) }}" class="btn-action btn-generate">
                                    <i class="fas fa-file-pdf"></i> Generate
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <h5>No payroll records found</h5>
                                    <p>Upload an Excel file to get started</p>
                                    <a href="{{ route('payroll.upload') }}" class="btn btn-primary">
                                        <i class="fas fa-upload"></i> Upload Data
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($payrolls->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $payrolls->links() }}
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.payroll-checkbox');

            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function getSelectedPayrolls() {
            const checkboxes = document.querySelectorAll('.payroll-checkbox:checked');
            return Array.from(checkboxes).map(cb => cb.value);
        }

        function generateSelected() {
            const selected = getSelectedPayrolls();
            if (selected.length === 0) {
                alert('Please select at least one payroll record.');
                return;
            }

            generatePayslips(selected);
        }

        function generateAll() {
            if (confirm('Are you sure you want to generate payslips for all records?')) {
                generatePayslips([]);
            }
        }

        function generatePayslips(payrollIds) {
            const formData = new FormData();
            payrollIds.forEach(id => formData.append('payroll_ids[]', id));

            fetch('{{ route("payslips.batch") }}', {
                    method: 'POST',
                    credentials: 'same-origin',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`Successfully generated ${data.generated.length} payslips!`);
                        location.reload();
                    } else {
                        alert('Error generating payslips: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while generating payslips.');
                });
        }
    </script>
</body>

</html>