<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Services\PayslipGeneratorService;
use Illuminate\Http\Request;

class PayslipController extends Controller
{
    protected $payslipGenerator;

    public function __construct(PayslipGeneratorService $payslipGenerator)
    {
        $this->payslipGenerator = $payslipGenerator;
    }

    public function generatePayslip(Payroll $payroll)
    {
        $force = (bool) request()->query('force', false);
        return $this->payslipGenerator->downloadPayslip($payroll, $force);
    }

    public function generateBatch(Request $request)
    {
        $payrollIds = $request->input('payroll_ids', []);

        $generated = $this->payslipGenerator->generateBatchPayslips($payrollIds);

        return response()->json([
            'success' => true,
            'message' => 'Payslips generated successfully',
            'generated' => $generated
        ]);
    }

    public function downloadPayslip(Payroll $payroll)
    {
        $force = (bool) request()->query('force', false);
        return $this->payslipGenerator->downloadPayslip($payroll, $force);
    }

    public function previewPayslip(Payroll $payroll, Request $request)
    {
        // Allow template override via query parameter
        $template = $request->get('template', config('payslip.template', 'outworx-template'));

        // Ensure template exists
        $templatePath = "payslips.{$template}";
        if (!view()->exists($templatePath)) {
            abort(404, "Template '{$template}' not found");
        }

        return view($templatePath, compact('payroll', 'template'));
    }
}
