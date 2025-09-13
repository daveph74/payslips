<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Payroll;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PayrollImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['employee_id']) && empty($row['name']) && empty($row['department'])) {
            return null;
        }

        // Find or create employee
        $employee = Employee::firstOrCreate(
            ['employee_id' => $row['employee_id']],
            [
                'name' => $row['name'],
                'department' => $row['department'],
                'email' => $row['email'] ?? null,
                'position' => $row['position'] ?? null,
                'hire_date' => isset($row['hire_date']) && !empty($row['hire_date']) ? $this->parseDate($row['hire_date']) : null,
            ]
        );

        // Calculate net pay
        $basicSalary = (float) $row['basic_salary'];
        $allowances = (float) ($row['allowances'] ?? 0);
        $overtime = (float) ($row['overtime'] ?? 0);
        $bonus = (float) ($row['bonus'] ?? 0);
        $deductions = (float) ($row['deductions'] ?? 0);
        $tax = (float) ($row['tax'] ?? 0);

        $netPay = $basicSalary + $allowances + $overtime + $bonus - $deductions - $tax;

        return new Payroll([
            'employee_id' => $employee->id,
            'period' => $row['period'],
            'basic_salary' => $basicSalary,
            'allowances' => $allowances,
            'overtime' => $overtime,
            'bonus' => $bonus,
            'deductions' => $deductions,
            'tax' => $tax,
            'net_pay' => $netPay,
            'pay_date' => $this->parseDate($row['pay_date']),
            'status' => 'pending',
        ]);
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
            'name' => 'required|string',
            'department' => 'required|string',
            'period' => 'required|string',
            'basic_salary' => 'required|numeric|min:0',
            'pay_date' => 'required',
        ];
    }

    public function prepareForValidation($data, $index)
    {
        // Skip validation for empty rows
        if (empty($data['employee_id']) && empty($data['name']) && empty($data['department'])) {
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
