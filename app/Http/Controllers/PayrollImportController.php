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
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt,text/plain,text/csv,application/csv,application/excel|max:10240'
        ]);

        try {
            $file = $request->file('excel_file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // Try to store the file
            $path = $file->storeAs('uploads', $filename, 'local');

            // Check if storage was successful
            if (!$path) {
                throw new \Exception('Failed to store uploaded file. Please check storage permissions.');
            }

            // Verify the file was actually stored
            if (!Storage::exists($path)) {
                throw new \Exception('File was not stored successfully. Path: ' . $path);
            }

            // Debug information
            \Log::info('File upload debug', [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'stored_path' => $path,
                'file_exists' => Storage::exists($path),
                'full_path' => Storage::path($path)
            ]);

            // Import the Excel file with better error handling
            $import = new PayrollImport();

            // Determine file type based on extension
            $extension = strtolower($file->getClientOriginalExtension());
            $readerType = match ($extension) {
                'xlsx' => \Maatwebsite\Excel\Excel::XLSX,
                'xls' => \Maatwebsite\Excel\Excel::XLS,
                'csv' => \Maatwebsite\Excel\Excel::CSV,
                default => \Maatwebsite\Excel\Excel::CSV // Default to CSV for better compatibility
            };

            // CSV files will be validated during the import process

            try {
                Excel::import($import, $path, 'local', $readerType);
            } catch (\Exception $e) {
                \Log::error('File import error: ' . $e->getMessage() . ' for file: ' . $path);

                // Handle different types of import errors
                if (str_contains($e->getMessage(), 'zip member') || str_contains($e->getMessage(), 'ZIP')) {
                    $errorMessage = 'The Excel file appears to be corrupted (ZIP error).';
                    $solutions = [
                        'Convert the file to CSV format and upload again',
                        'Use the cleaner command: <code>php artisan excel:clean your_file.csv cleaned_file.csv</code>',
                        'Re-save the Excel file in Excel and try again'
                    ];
                } elseif (str_contains($e->getMessage(), 'Invalid Spreadsheet file')) {
                    $errorMessage = 'The file format is not recognized or contains invalid data.';
                    $solutions = [
                        'Ensure the file is a valid Excel (.xlsx, .xls) or CSV (.csv) file',
                        'Remove any formulas and save as values only',
                        'Check for special characters or unusual formatting',
                        'Try saving the file as CSV format instead'
                    ];
                } else {
                    $errorMessage = 'An error occurred while importing the file.';
                    $solutions = [
                        'Check that all required columns are present',
                        'Ensure numeric fields contain valid numbers',
                        'Remove any empty rows or columns',
                        'Try the cleaner command: <code>php artisan excel:clean your_file.csv cleaned_file.csv</code>'
                    ];
                }

                // Clean up uploaded file
                Storage::delete($path);

                return redirect()->route('payroll.upload')
                    ->with('error', $errorMessage . '<br><br><strong>Solutions to try:</strong><br>' .
                        implode('<br>', array_map(fn($s) => "• $s", $solutions)));
            }

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
        $payrolls = Payroll::with('employee')->latest()->paginate(25);
        return view('payroll.index', compact('payrolls'));
    }
}
