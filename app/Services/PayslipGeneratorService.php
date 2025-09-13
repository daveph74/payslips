<?php

namespace App\Services;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayslipGeneratorService
{
    public function generatePayslip(Payroll $payroll): string
    {
        $template = config('payslip.template', 'outworx-template');
        $pdf = Pdf::loadView("payslips.{$template}", compact('payroll'));
        $pdf->setPaper('A4', 'portrait');

        $filename = "payslip_{$payroll->employee->employee_id}_{$payroll->period}.pdf";
        $path = "payslips/{$filename}";

        // Store the PDF
        Storage::disk('local')->put($path, $pdf->output());

        // Update payroll status
        $payroll->update(['status' => 'generated']);

        return $path;
    }

    public function generateBatchPayslips(array $payrollIds = null): array
    {
        $query = Payroll::with('employee');

        if ($payrollIds) {
            $query->whereIn('id', $payrollIds);
        }

        $payrolls = $query->get();
        $generated = [];

        foreach ($payrolls as $payroll) {
            $path = $this->generatePayslip($payroll);
            $generated[] = [
                'payroll_id' => $payroll->id,
                'employee_name' => $payroll->employee->name,
                'period' => $payroll->period,
                'file_path' => $path
            ];
        }

        return $generated;
    }

    public function downloadPayslip(Payroll $payroll)
    {
        $filename = "payslip_{$payroll->employee->employee_id}_{$payroll->period}.pdf";
        $path = "payslips/{$filename}";

        if (!Storage::disk('local')->exists($path)) {
            $this->generatePayslip($payroll);
        }

        return Storage::disk('local')->download($path, $filename);
    }
}
