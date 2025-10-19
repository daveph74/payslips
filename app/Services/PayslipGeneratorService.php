<?php

namespace App\Services;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PayslipGeneratorService
{
    public function generatePayslip(Payroll $payroll): string
    {
        try {
            $template = config('payslip.template', 'outworx-template');
            \Log::info("Generating PDF for payroll ID: {$payroll->id}, template: {$template}");

            // Check if template exists
            if (!view()->exists("payslips.{$template}")) {
                throw new \Exception("Template 'payslips.{$template}' not found");
            }

            // Test template rendering first
            try {
                $html = view("payslips.{$template}", compact('payroll'))->render();
                if (empty($html)) {
                    throw new \Exception("Template rendered empty HTML");
                }
                \Log::info("Template rendered successfully, HTML length: " . strlen($html));
            } catch (\Exception $e) {
                throw new \Exception("Template rendering failed: " . $e->getMessage());
            }

            $pdf = Pdf::loadView("payslips.{$template}", compact('payroll'));
            $pdf->setPaper('A4', 'portrait');

            $filename = $this->buildFilename($payroll);
            $path = "payslips/{$filename}";

            \Log::info("Attempting to store PDF at: {$path}");

            // Store the PDF
            $pdfOutput = $pdf->output();
            if (empty($pdfOutput)) {
                throw new \Exception("PDF output is empty");
            }

            // Try to store the PDF file
            $stored = Storage::disk('local')->put($path, $pdfOutput);
            if (!$stored) {
                // If local disk fails, try public disk
                \Log::warning("Local disk storage failed, trying public disk");
                $stored = Storage::disk('public')->put($path, $pdfOutput);
                if (!$stored) {
                    throw new \Exception("Failed to store PDF file on both local and public disks. Path: {$path}");
                }
                \Log::info("PDF stored successfully on public disk: {$path}");
            } else {
                \Log::info("PDF stored successfully on local disk: {$path}");
            }

            // Update payroll status
            $payroll->update(['status' => 'generated']);

            return $path;
        } catch (\Exception $e) {
            \Log::error("PDF generation failed for payroll ID {$payroll->id}: " . $e->getMessage());
            throw $e;
        }
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

    public function downloadPayslip(Payroll $payroll, bool $force = false)
    {
        $filename = $this->buildFilename($payroll);
        $legacyFilename = $this->buildLegacyFilename($payroll);
        $path = "payslips/{$filename}";
        $legacyPath = "payslips/{$legacyFilename}";

        // Force regeneration if requested
        if ($force) {
            try {
                // Attempt to overwrite by regenerating
                $this->generatePayslip($payroll);
            } catch (\Exception $e) {
                \Log::warning("Forced regeneration failed for {$filename}: " . $e->getMessage());
            }
        } else {
            // Ensure the file exists: prefer new naming, fallback to legacy naming if present
            $existsNew = Storage::disk('local')->exists($path) || Storage::disk('public')->exists($path);
            $existsLegacy = Storage::disk('local')->exists($legacyPath) || Storage::disk('public')->exists($legacyPath);

            if (!$existsNew && !$existsLegacy) {
                $this->generatePayslip($payroll);
            }
        }

        // Decide which path to use (new preferred, legacy fallback)
        $useLegacy = false;
        if (!Storage::disk('local')->exists($path) && !Storage::disk('public')->exists($path)) {
            if (Storage::disk('local')->exists($legacyPath) || Storage::disk('public')->exists($legacyPath)) {
                $useLegacy = true;
            } else {
                // Verify file exists after generation
                throw new \Exception("Failed to generate payslip PDF for {$payroll->employee->name}");
            }
        }

        try {
            // Try local disk first
            if (!$useLegacy && Storage::disk('local')->exists($path)) {
                return Storage::disk('local')->download($path, $filename);
            }
            if ($useLegacy && Storage::disk('local')->exists($legacyPath)) {
                return Storage::disk('local')->download($legacyPath, $legacyFilename);
            }

            // Try public disk if local doesn't have the file
            if (!$useLegacy && Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->download($path, $filename);
            }
            if ($useLegacy && Storage::disk('public')->exists($legacyPath)) {
                return Storage::disk('public')->download($legacyPath, $legacyFilename);
            }

            throw new \Exception("File not found on any disk");
        } catch (\Exception $e) {
            // Log the original error for debugging
            \Log::warning("Storage download failed for " . ($useLegacy ? $legacyFilename : $filename) . ": " . $e->getMessage());

            // If that fails, try a direct file response
            $fullPath = Storage::disk('local')->path($useLegacy ? $legacyPath : $path);
            if (!file_exists($fullPath)) {
                $fullPath = Storage::disk('public')->path($useLegacy ? $legacyPath : $path);
            }

            if (!file_exists($fullPath)) {
                throw new \Exception("Payslip file not found at: {$fullPath}");
            }

            \Log::info("Using direct file download for " . ($useLegacy ? $legacyFilename : $filename) . " from {$fullPath}");

            return response()->download($fullPath, $filename, [
                'Content-Type' => 'application/pdf',
            ]);
        }
    }

    private function buildFilename(Payroll $payroll): string
    {
        $employeeId = $payroll->employee->employee_id;
        $employeeName = $payroll->employee->name;
        $period = $payroll->period;

        // Strip characters unsafe for filenames across platforms
        $safeEmployeeName = preg_replace('/[\\\\\/\:\*\?\"\<\>\|]+/', '', $employeeName);
        $safePeriod = preg_replace('/[\\\\\/\:\*\?\"\<\>\|]+/', '', $period);

        return "payslip_{$employeeId}_{$safeEmployeeName}_{$safePeriod}.pdf";
    }

    private function buildLegacyFilename(Payroll $payroll): string
    {
        return "payslip_{$payroll->employee->employee_id}_{$payroll->period}.pdf";
    }
}
