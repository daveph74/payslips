<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'excel:clean {input} {output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Excel file by removing empty rows and fixing common issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inputFile = $this->argument('input');
        $outputFile = $this->argument('output');

        if (!file_exists($inputFile)) {
            $this->error("Input file '$inputFile' not found.");
            return 1;
        }

        $this->info("Cleaning Excel file: $inputFile");
        $this->info("Output file: $outputFile");

        $input = fopen($inputFile, 'r');
        $output = fopen($outputFile, 'w');

        if (!$input || !$output) {
            $this->error("Could not open files.");
            return 1;
        }

        $header = fgetcsv($input);
        if (!$header) {
            $this->error("Could not read header row.");
            return 1;
        }

        // Write header
        fputcsv($output, $header);

        $rowCount = 0;
        $cleanedCount = 0;
        $skippedCount = 0;

        while (($row = fgetcsv($input)) !== false) {
            $rowCount++;

            // Check if row is empty
            $isEmpty = true;
            foreach ($row as $cell) {
                if (!empty(trim($cell))) {
                    $isEmpty = false;
                    break;
                }
            }

            if ($isEmpty) {
                $skippedCount++;
                $this->line("Skipping empty row $rowCount");
                continue;
            }

            // Check required fields
            $employeeId = trim($row[0] ?? '');
            $name = trim($row[1] ?? '');
            $department = trim($row[2] ?? '');

            if (empty($employeeId) || empty($name) || empty($department)) {
                $skippedCount++;
                $this->line("Skipping row $rowCount - missing required fields");
                continue;
            }

            // Clean row
            $cleanedRow = array_map(function ($cell) {
                return trim($cell);
            }, $row);

            fputcsv($output, $cleanedRow);
            $cleanedCount++;
        }

        fclose($input);
        fclose($output);

        $this->info("\nCleaning complete!");
        $this->info("Total rows processed: $rowCount");
        $this->info("Rows kept: $cleanedCount");
        $this->info("Rows skipped: $skippedCount");
        $this->info("Output saved to: $outputFile");

        if ($cleanedCount > 0) {
            $this->info("✅ Your cleaned file is ready to upload!");
        } else {
            $this->error("❌ No valid rows found. Please check your input file.");
        }

        return 0;
    }
}
