<?php

/**
 * Excel File Cleaner Utility
 * 
 * This script helps clean Excel files by removing empty rows and fixing common issues.
 * 
 * Usage: php clean_excel.php input_file.csv output_file.csv
 */

if ($argc < 3) {
    echo "Usage: php clean_excel.php input_file.csv output_file.csv\n";
    echo "Example: php clean_excel.php payroll_data.csv payroll_data_clean.csv\n";
    exit(1);
}

$inputFile = $argv[1];
$outputFile = $argv[2];

if (!file_exists($inputFile)) {
    echo "Error: Input file '$inputFile' not found.\n";
    exit(1);
}

echo "Cleaning Excel file: $inputFile\n";
echo "Output file: $outputFile\n\n";

$input = fopen($inputFile, 'r');
$output = fopen($outputFile, 'w');

if (!$input || !$output) {
    echo "Error: Could not open files.\n";
    exit(1);
}

$header = fgetcsv($input);
if (!$header) {
    echo "Error: Could not read header row.\n";
    exit(1);
}

// Write header
fputcsv($output, $header);

$rowCount = 0;
$cleanedCount = 0;
$skippedCount = 0;

while (($row = fgetcsv($input)) !== false) {
    $rowCount++;

    // Check if row is empty or has only empty values
    $isEmpty = true;
    foreach ($row as $cell) {
        if (!empty(trim($cell))) {
            $isEmpty = false;
            break;
        }
    }

    if ($isEmpty) {
        $skippedCount++;
        echo "Skipping empty row $rowCount\n";
        continue;
    }

    // Check if required fields are present
    $employeeId = trim($row[0] ?? '');
    $name = trim($row[1] ?? '');
    $department = trim($row[2] ?? '');

    if (empty($employeeId) || empty($name) || empty($department)) {
        $skippedCount++;
        echo "Skipping row $rowCount - missing required fields (Employee ID, Name, or Department)\n";
        continue;
    }

    // Clean and format the row
    $cleanedRow = array_map(function ($cell) {
        return trim($cell);
    }, $row);

    fputcsv($output, $cleanedRow);
    $cleanedCount++;
}

fclose($input);
fclose($output);

echo "\nCleaning complete!\n";
echo "Total rows processed: $rowCount\n";
echo "Rows kept: $cleanedCount\n";
echo "Rows skipped: $skippedCount\n";
echo "Output saved to: $outputFile\n";

if ($cleanedCount > 0) {
    echo "\n✅ Your cleaned file is ready to upload!\n";
} else {
    echo "\n❌ No valid rows found. Please check your input file.\n";
}
