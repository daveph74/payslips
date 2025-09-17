<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PayrollImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Map column names to handle different Excel formats
        $employeeId = $row['employee_id'] ?? $row['Employee_ID'] ?? null;
        $name = $row['name'] ?? $row['Name'] ?? null;
        $department = $row['department'] ?? $row['Department'] ?? null;
        $period = $row['period'] ?? $row['Pay_Period'] ?? $row['pay_period'] ?? null;
        $payDate = $row['pay_date'] ?? $row['Pay_Date'] ?? $row['Pay_Date'] ?? null;

        // Skip empty rows
        if (empty($employeeId) && empty($name) && empty($department)) {
            return null;
        }

        // Find or create employee
        $employee = Employee::firstOrCreate(
            ['employee_id' => $employeeId],
            [
                'name' => $name,
                'department' => $department,
                'email' => $row['email'] ?? $row['Email'] ?? null,
                'position' => $row['position'] ?? $row['Position'] ?? null,
                'hire_date' => isset($row['hire_date']) && !empty($row['hire_date']) ? $this->parseDate($row['hire_date']) : null,
            ]
        );

        // Calculate net pay - handle different column names including new underscore format
        // Also clean comma-formatted numbers
        $basicSalary = (float) $this->cleanNumber($row['basic_salary'] ?? $row['Basic_Salary'] ?? 0);
        $allowances = (float) $this->cleanNumber($row['allowances'] ?? $row['Allowances'] ?? 0);
        $overtime = (float) $this->cleanNumber($row['overtime'] ?? $row[' Overtime '] ?? $row['Overtime'] ?? 0);
        $bonus = (float) $this->cleanNumber($row['holiday'] ?? $row['Holiday'] ?? $row['bonus'] ?? $row['Bonus'] ?? $row['Holiday Premium'] ?? $row[' Holiday '] ?? 0);

        // Handle different deduction column names - updated to match CSV exactly (with lowercase from WithHeadingRow)
        $socialSecurity = (float) $this->cleanNumber($row['social_security_system'] ?? $row['Social_Security_System'] ?? $row['Social Security System'] ?? 0);
        $philHealth = (float) $this->cleanNumber($row['philhealth'] ?? $row['PhilHealth'] ?? 0);
        $pagIbig = (float) $this->cleanNumber($row['pag_ibig'] ?? $row['Pag_ibig'] ?? $row['Pag-ibig'] ?? 0);
        $tax = (float) $this->cleanNumber($row['withholding_tax'] ?? $row['Withholding_Tax'] ?? $row['Withholding Tax'] ?? $row['tax'] ?? 0);
        $loans = (float) $this->cleanNumber($row['loans'] ?? $row['Loans'] ?? 0);
        $otherDeductions = (float) $this->cleanNumber($row['others_authorized_deductions'] ?? $row['Others_Authorized_Deductions'] ?? $row['Others Authorized Deductions'] ?? 0);

        // Calculate total deductions
        $totalDeductions = $socialSecurity + $philHealth + $pagIbig + $tax + $loans + $otherDeductions;
        $deductions = (float) $this->cleanNumber($row['total_deductions'] ?? $row['Total_Deductions'] ?? $row['Total Deductions'] ?? $row['deductions'] ?? $totalDeductions);

        // If we don't have a total deductions column value, use our calculated total
        if ($deductions == 0 && $totalDeductions > 0) {
            $deductions = $totalDeductions;
        }

        // If we have a calculated net pay, use it; otherwise calculate it
        $netPay = $this->cleanNumber($row[' Net_Pay '] ?? $row['Net_Pay'] ?? $row['Net Pay'] ?? ($basicSalary + $allowances + $overtime + $bonus - $deductions));

        return new Payroll([
            'employee_id' => $employee->id,
            'period' => $period,
            'basic_salary' => $basicSalary,
            'allowances' => $allowances,
            'overtime' => $overtime,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'tax' => $tax,
            'net_pay' => (float) $netPay,
            'pay_date' => $this->parseDate($payDate),
            'status' => 'pending',
            // New detailed fields from CSV
            'total_earnings' => (float) $this->cleanNumber($row['total_earnings'] ?? $row['Total_Earnings'] ?? $row['Total Earnings'] ?? 0),
            'social_security_system' => $socialSecurity,
            'philhealth' => $philHealth,
            'pag_ibig' => $pagIbig,
            'withholding_tax' => $tax,
            'loans' => $loans,
            'unpaid_absences_tardiness' => (float) $this->cleanNumber($row['unpaid_absences_tardiness'] ?? $row['Unpaid_Absences_Tardiness'] ?? $row['Unpaid Absences, Tardiness'] ?? 0),
            'others_authorized_deductions' => $otherDeductions,
            'total_deductions' => $deductions, // Use the calculated total deductions
        ]);
    }

    /**
     * Clean number formatting (remove commas, quotes, etc.)
     */
    private function cleanNumber($value)
    {
        if (empty($value)) {
            return 0;
        }

        // Convert to string and remove commas, quotes, and extra spaces
        $cleaned = str_replace([',', '"', "'", ' '], '', (string) $value);

        // Return as float, defaulting to 0 if not numeric
        return is_numeric($cleaned) ? (float) $cleaned : 0;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return null;
        }

        // Handle Excel date serial numbers (like 44927)
        if (is_numeric($dateValue)) {
            try {
                // Excel date serial number to Carbon date
                $excelDate = (int) $dateValue;
                $unixTimestamp = ($excelDate - 25569) * 86400; // Excel epoch is 1900-01-01
                return Carbon::createFromTimestamp($unixTimestamp);
            } catch (\Exception $e) {
                // If it fails, try parsing as regular date
            }
        }

        // Try various date formats
        $formats = [
            'Y-m-d',           // 2024-01-31
            'm/d/Y',           // 01/31/2024
            'Y-m-d H:i:s',     // 2024-01-31 00:00:00
            'm/d/Y H:i:s',     // 01/31/2024 00:00:00
            'd-m-Y',           // 31-01-2024
            'Y/m/d',           // 2024/01/31
            'd/m/Y',           // 31/01/2024 (be careful with this one)
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateValue);
                if ($date && $date->year >= 1900 && $date->year <= 2100) {
                    return $date;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Last resort: try Carbon's parse method
        try {
            return Carbon::parse($dateValue);
        } catch (\Exception $e) {
            throw new \Exception("Invalid date format: '{$dateValue}'. Please use formats like YYYY-MM-DD, MM/DD/YYYY, or DD/MM/YYYY");
        }
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|string',
            'Name' => 'required|string',
            'Department' => 'required|string',
            'Pay_Period' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'Pay_Date' => 'required',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        // Skip validation for empty rows - check multiple possible column names
        $employeeId = $data['employee_id'] ?? $data['Employee_ID'] ?? null;
        $name = $data['name'] ?? $data['Name'] ?? null;
        $department = $data['department'] ?? $data['Department'] ?? null;

        if (empty($employeeId) && empty($name) && empty($department)) {
            return [];
        }

        return $data;
    }

    public function customValidationMessages()
    {
        return [
            'employee_id.required' => 'Employee ID is required in row :attribute',
            'name.required' => 'Employee name is required in row :attribute',
            'department.required' => 'Department is required in row :attribute',
            'period.required' => 'Pay period is required in row :attribute',
            'basic_salary.required' => 'Basic salary is required in row :attribute',
            'basic_salary.numeric' => 'Basic salary must be a number in row :attribute',
            'pay_date.required' => 'Pay date is required in row :attribute',
        ];
    }
}
