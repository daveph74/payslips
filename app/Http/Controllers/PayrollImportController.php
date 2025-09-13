<?php

namespace App\Http\Controllers;

use App\Imports\PayrollImport;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class PayrollImportController extends Controller
{
    public function showUploadForm()
    {
        return view('payroll.upload');
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename);

            // Import the Excel file with better error handling
            $import = new PayrollImport();
            Excel::import($import, $path);

            // Clean up uploaded file
            Storage::delete($path);

            return redirect()->route('payroll.upload')
                ->with('success', 'Payroll data imported successfully!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            // Handle validation errors
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            Storage::delete($path ?? '');

            return redirect()->route('payroll.upload')
                ->with('error', 'Validation errors found:<br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            // Clean up uploaded file
            Storage::delete($path ?? '');

            return redirect()->route('payroll.upload')
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $payrolls = Payroll::with('employee')->latest()->paginate(20);
        return view('payroll.index', compact('payrolls'));
    }
}
