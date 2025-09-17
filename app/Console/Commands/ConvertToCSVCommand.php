<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ConvertToCSVCommand extends Command
{
    protected $signature = 'excel:convert {input} {output}';
    protected $description = 'Convert Excel file to clean CSV format';

    public function handle()
    {
        $inputFile = $this->argument('input');
        $outputFile = $this->argument('output');

        if (!file_exists($inputFile)) {
            $this->error("Input file '$inputFile' not found.");
            return 1;
        }

        $this->info("Converting: $inputFile");
        $this->info("Output: $outputFile");

        try {
            // Try to read as Excel first
            $extension = strtolower(pathinfo($inputFile, PATHINFO_EXTENSION));

            if (in_array($extension, ['xlsx', 'xls'])) {
                // Read Excel file
                $readerType = $extension === 'xlsx' ? \Maatwebsite\Excel\Excel::XLSX : \Maatwebsite\Excel\Excel::XLS;
                $data = Excel::toArray([], $inputFile, null, $readerType);
            } else {
                // Read CSV file
                $data = Excel::toArray([], $inputFile, null, \Maatwebsite\Excel\Excel::CSV);
            }

            if (empty($data) || empty($data[0])) {
                $this->error("No data found in the input file.");
                return 1;
            }

            $this->info("Found " . count($data[0]) . " rows of data");

            // Clean and write to CSV
            $output = fopen($outputFile, 'w');
            $rowCount = 0;
            $cleanedCount = 0;
            $skippedCount = 0;

            foreach ($data[0] as $row) {
                $rowCount++;

                // Skip completely empty rows
                $isEmpty = true;
                foreach ($row as $cell) {
                    if (!empty(trim($cell))) {
                        $isEmpty = false;
                        break;
                    }
                }

                if ($isEmpty && $rowCount > 1) { // Keep header even if it looks empty
                    $skippedCount++;
                    continue;
                }

                // Clean the row
                $cleanedRow = array_map(function ($cell) {
                    $cell = trim($cell);

                    // Remove formulas (anything starting with =)
                    if (is_string($cell) && str_starts_with($cell, '=')) {
                        $cell = '';
                    }

                    // Clean number formatting
                    if (is_string($cell) && preg_match('/^["\']?[\d,]+\.?\d*["\']?$/', $cell)) {
                        $cell = str_replace([',', '"', "'"], '', $cell);
                    }

                    return $cell;
                }, $row);

                fputcsv($output, $cleanedRow);
                $cleanedCount++;
            }

            fclose($output);

            $this->info("\nConversion complete!");
            $this->info("Total rows processed: $rowCount");
            $this->info("Rows converted: $cleanedCount");
            $this->info("Rows skipped: $skippedCount");
            $this->info("✅ Clean CSV file created: $outputFile");

            return 0;
        } catch (\Exception $e) {
            $this->error("Error converting file: " . $e->getMessage());
            return 1;
        }
    }
}
