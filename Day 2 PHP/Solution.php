<?php
$inputFileName = 'input.txt';

$reports = getInputFromFile($inputFileName);

$validReports = [];
$validReportsWithDampener = [];
foreach ($reports as $report) {
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

function getInputFromFile(string $fileName): array
{
    if (!file_exists($fileName)) {
        throw new Exception(sprintf('No input file with name: %s', $fileName));
    }

    $input = file($fileName, FILE_IGNORE_NEW_LINES);
    if (empty($input) || !is_array($input)) {
        throw new Exception(sprintf('Empty input file with name: %s', $fileName));
    }

    return array_map(fn(string $report) => explode(' ', $report), $input);
}

function validateReportWithDampener(array $report): bool
{
    $validReport = validateReport($report);
    if ($validReport) {
        return true;
    }

    foreach ($report as $index => $level) {
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
    $isIncreasing = areLevelsIncreasing($report);
    $isDecreasing = areLevelsDecreasing($report);
    if ($isIncreasing === $isDecreasing) {
        return false;
    }

    $previousLevel = null;
    foreach ($report as $level) {
        if (is_null($previousLevel)) {
            $previousLevel = $level;

            continue;
        }

        $validLevel = validateLevel($level, $previousLevel, $isIncreasing, $isDecreasing);
        if (!$validLevel) {
            return false;
        }

        $previousLevel = $level;
    }

    return true;
}

function areLevelsIncreasing(array $report): bool
{
    return sortReport($report) === $report;
}

function areLevelsDecreasing(array $report): bool
{
    return sortReport($report) === array_reverse($report);
}

function sortReport(array $report): array
{
    sort($report, SORT_NUMERIC);

    return $report;
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
