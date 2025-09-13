<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Payroll Data</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .upload-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .upload-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .upload-header h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .upload-header p {
            color: #666;
            font-size: 16px;
        }

        .upload-form {
            background-color: #f8f9fa;
            padding: 30px;
            border-radius: 8px;
            border: 2px dashed #dee2e6;
            transition: all 0.3s ease;
        }

        .upload-form:hover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .file-input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-input-label {
            display: block;
            padding: 20px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .file-input-label:hover {
            background-color: #0056b3;
        }

        .file-info {
            margin-top: 15px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 4px;
            display: none;
        }

        .sample-format {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 20px;
            margin-top: 30px;
        }

        .sample-format h4 {
            color: #856404;
            margin-bottom: 15px;
        }

        .sample-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .sample-table th,
        .sample-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }

        .sample-table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .btn-generate {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: 600;
            transition: transform 0.2s ease;
        }

        .btn-generate:hover {
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="upload-container">
            <div class="upload-header">
                <h1>📊 Payroll Data Upload</h1>
                <p>Upload your Excel file containing employee payroll information</p>
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <div>{!! session('error') !!}</div>
                <hr>
                <small>
                    <strong>Quick Fix:</strong> Use the cleaner command to fix your file:<br>
                    <code>php artisan excel:clean your_file.csv cleaned_file.csv</code>
                </small>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <form action="{{ route('payroll.upload.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="upload-form">
                    <div class="file-input-wrapper">
                        <input type="file" name="excel_file" id="excel_file" class="file-input" accept=".xlsx,.xls,.csv" required>
                        <label for="excel_file" class="file-input-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <div>Click to select Excel file or drag and drop</div>
                            <small>Supported formats: .xlsx, .xls, .csv (Max: 10MB)</small>
                        </label>
                    </div>
                    <div class="file-info" id="fileInfo">
                        <strong>Selected file:</strong> <span id="fileName"></span>
                        <br>
                        <strong>Size:</strong> <span id="fileSize"></span>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-generate">
                        <i class="fas fa-upload"></i> Upload & Process Payroll Data
                    </button>
                </div>
            </form>

            <div class="sample-format">
                <h4>📋 Expected Excel Format</h4>
                <p>Your Excel file should contain the following columns (with headers):</p>
                <table class="sample-table">
                    <thead>
                        <tr>
                            <th>employee_id</th>
                            <th>name</th>
                            <th>department</th>
                            <th>email</th>
                            <th>position</th>
                            <th>period</th>
                            <th>basic_salary</th>
                            <th>allowances</th>
                            <th>overtime</th>
                            <th>bonus</th>
                            <th>deductions</th>
                            <th>tax</th>
                            <th>pay_date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>EMP001</td>
                            <td>John Doe</td>
                            <td>IT</td>
                            <td>john@company.com</td>
                            <td>Developer</td>
                            <td>2024-01</td>
                            <td>5000.00</td>
                            <td>500.00</td>
                            <td>200.00</td>
                            <td>0.00</td>
                            <td>100.00</td>
                            <td>800.00</td>
                            <td>2024-01-31</td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-3">
                    <a href="{{ route('payroll.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> View All Payroll Records
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('excel_file').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');

            if (file) {
                fileName.textContent = file.name;
                fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        });

        // Drag and drop functionality
        const uploadForm = document.querySelector('.upload-form');

        uploadForm.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadForm.style.borderColor = '#007bff';
            uploadForm.style.backgroundColor = '#e3f2fd';
        });

        uploadForm.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadForm.style.borderColor = '#dee2e6';
            uploadForm.style.backgroundColor = '#f8f9fa';
        });

        uploadForm.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadForm.style.borderColor = '#dee2e6';
            uploadForm.style.backgroundColor = '#f8f9fa';

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                document.getElementById('excel_file').files = files;
                document.getElementById('excel_file').dispatchEvent(new Event('change'));
            }
        });
    </script>
</body>

</html>