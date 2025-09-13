# Payroll Payslip Generator

A Laravel 11 application that imports employee payroll data from Excel files and generates professional payslip PDFs.

## Features

- 📊 **Excel Import**: Upload and parse Excel/CSV files containing payroll data
- 📄 **PDF Generation**: Generate professional payslips using a beautiful template
- 🎯 **Batch Processing**: Generate multiple payslips at once
- 👥 **Employee Management**: Automatic employee creation and management
- 📱 **Responsive UI**: Modern, mobile-friendly interface
- 🔍 **Admin Dashboard**: View and manage all payroll records

## Technology Stack

- **Laravel 11** - PHP Framework
- **MySQL** - Database
- **maatwebsite/excel** - Excel file processing
- **barryvdh/laravel-dompdf** - PDF generation
- **Bootstrap 5** - Frontend styling

## Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd payroll-payslip-generator
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database configuration**
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=payroll_payslip_generator
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run migrations**
   ```bash
   php artisan migrate
   ```

6. **Start the development server**
   ```bash
   php artisan serve
   ```

## Usage

### 1. Upload Payroll Data

1. Navigate to the upload page
2. Select an Excel/CSV file with the following columns:
   - `employee_id` - Unique employee identifier
   - `name` - Employee full name
   - `department` - Department name
   - `email` - Employee email (optional)
   - `position` - Job position (optional)
   - `period` - Pay period (e.g., "2024-01")
   - `basic_salary` - Base salary amount
   - `allowances` - Additional allowances (optional)
   - `overtime` - Overtime pay (optional)
   - `bonus` - Bonus amount (optional)
   - `deductions` - Deductions (optional)
   - `tax` - Tax amount (optional)
   - `pay_date` - Payment date

3. Click "Upload & Process Payroll Data"

### 2. Generate Payslips

#### Individual Payslips
- Go to the payroll records page
- Click "Generate" next to any pending record
- The PDF will be automatically downloaded

#### Batch Generation
- Select multiple records using checkboxes
- Click "Generate Selected Payslips" or "Generate All Payslips"
- All selected payslips will be generated

### 3. Download Payslips
- Generated payslips are stored in `storage/app/payslips/`
- Click "Download" to get individual payslips
- Files are named: `payslip_{employee_id}_{period}.pdf`

## API Endpoints

### Payroll Import
- `GET /payroll/upload` - Show upload form
- `POST /payroll/upload` - Process uploaded file
- `GET /payroll` - List all payroll records

### Payslip Generation
- `GET /payslips/{payroll}` - Generate individual payslip
- `GET /payslips/{payroll}/download` - Download payslip
- `POST /payslips/batch` - Generate multiple payslips

## File Structure

```
app/
├── Http/Controllers/
│   ├── PayrollImportController.php
│   └── PayslipController.php
├── Imports/
│   └── PayrollImport.php
├── Models/
│   ├── Employee.php
│   └── Payroll.php
└── Services/
    └── PayslipGeneratorService.php

resources/views/
├── payslips/
│   └── template.blade.php
└── payroll/
    ├── upload.blade.php
    └── index.blade.php

storage/app/
├── payslips/          # Generated PDF files
└── samples/           # Sample data files
```

## Database Schema

### Employees Table
- `id` - Primary key
- `employee_id` - Unique employee identifier
- `name` - Employee name
- `department` - Department
- `email` - Email address
- `position` - Job position
- `hire_date` - Hire date
- `created_at`, `updated_at` - Timestamps

### Payrolls Table
- `id` - Primary key
- `employee_id` - Foreign key to employees
- `period` - Pay period
- `basic_salary` - Base salary
- `allowances` - Additional allowances
- `overtime` - Overtime pay
- `bonus` - Bonus amount
- `deductions` - Deductions
- `tax` - Tax amount
- `net_pay` - Calculated net pay
- `pay_date` - Payment date
- `status` - Record status (pending/generated/sent)
- `created_at`, `updated_at` - Timestamps

## Sample Data

A sample CSV file is provided at `storage/app/samples/sample_payroll_data.csv` for testing purposes.

## Customization

### Payslip Template
The payslip template can be customized by editing `resources/views/payslips/template.blade.php`. The template includes:
- Company branding
- Employee information
- Salary breakdown
- Professional styling

### Excel Format
The expected Excel format can be modified in `app/Imports/PayrollImport.php` by updating the column mappings and validation rules.

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL is running
   - Check database credentials in `.env`
   - Create the database if it doesn't exist

2. **File Upload Issues**
   - Check file permissions on `storage/` directory
   - Ensure file size is under 10MB
   - Verify file format is .xlsx, .xls, or .csv

3. **PDF Generation Errors**
   - Check if `storage/app/payslips/` directory exists
   - Ensure proper permissions on storage directories
   - Verify DomPDF is properly installed

### Logs
Check Laravel logs in `storage/logs/laravel.log` for detailed error information.

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please create an issue in the repository or contact the development team.