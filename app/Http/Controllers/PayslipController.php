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
        return $this->payslipGenerator->downloadPayslip($payroll);
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
        return $this->payslipGenerator->downloadPayslip($payroll);
    }
}
