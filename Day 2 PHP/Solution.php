<?php
$inputFileName = 'input.txt';

if (!file_exists($inputFileName)) {
    throw new Exception(sprintf('No input file with name: %s', $inputFileName));
}

$input = file($inputFileName, FILE_IGNORE_NEW_LINES);
if (empty($input) || !is_array($input)) {
    throw new Exception(sprintf('Empty input file with name: %s', $inputFileName));
}

$formattedReports = array_map(fn($report) => explode(' ', $report), $input);

$validReports = [];
$validReportsWithDampener = [];
foreach ($formattedReports as $report) {
    if (validateReport($report)) {
        $validReports[] = $report;
    }

    if (validateReportWithDampener($report)) {
        $validReportsWithDampener[] = $report;
    }
}

echo sprintf('Valid Reports (Part 1): %s', count($validReports));
echo PHP_EOL;
echo sprintf('Valid Reports with Dampener (Part 2): %s', count($validReportsWithDampener));

function validateReportWithDampener(array $report): bool
{
    $validReport = validateReport($report);
    if ($validReport) {
        return true;
    }

    for ($index = 0; $index < (count($report)); $index++) {
        $reportToModify = $report;
        unset($reportToModify[$index]);
        if (validateReport(array_values($reportToModify))) {
            return true;
        }
    }

    return false;
}

function validateReport(array $report): bool
{
    $previousLevel = null;
    $isIncreasing = false;
    $isDecreasing = false;
    foreach ($report as $level) {
        if (is_null($previousLevel)) {
            $previousLevel = $level;

            continue;
        }

        $validLevel = validateLevel($level, $previousLevel, $isIncreasing, $isDecreasing);
        if (!$validLevel) {
            return false;
        }

        if ($level > $previousLevel) {
            $isIncreasing = true;
        }

        if ($level < $previousLevel) {
            $isDecreasing = true;
        }

        $previousLevel = $level;
    }

    return true;
}

function validateLevel(string $level, string $previousLevel, bool $isIncreasing, bool $isDecreasing): bool
{
    if (abs($level - $previousLevel) > 3 || $level === $previousLevel) {
        return false;
    }

    if ($level > $previousLevel && $isDecreasing) {
        return false;
    }

    if ($level < $previousLevel && $isIncreasing) {
        return false;
    }

    return true;
}
