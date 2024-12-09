<?php

$inputFileName = 'input.txt';
$input = getInputFromFile($inputFileName);

echo sprintf('Valid Calibration Results (Part 1): %s', calculateValidCalibrationResults($input, false));
echo PHP_EOL;
echo sprintf('Valid Calibration Results (Part 2): %s', calculateValidCalibrationResults($input, true));

function calculateValidCalibrationResults(array $input, bool $useConcatenation = false): int
{
    $sum = 0;
    foreach ($input as $index => $test) {
        if (!isValidTest($test['values'], 0, $test['total'], 0, $useConcatenation)) {
            continue;
        }

        $sum += $test['total'];
    }

    return $sum;
}

function isValidTest(array $input, int $counter, int $target, int $index, bool $useConcatenation): bool
{
    if ($index === count($input)) {
        return $counter === $target;
    }

    if ($counter > $target) {
        return false;
    }

    return isValidTest($input, $counter + $input[$index], $target, $index + 1, $useConcatenation)
        || isValidTest($input, $counter * $input[$index], $target, $index + 1, $useConcatenation)
        || ($useConcatenation && isValidTest($input, sprintf('%s%s', $counter, $input[$index]), $target, $index + 1, true));
}

function getInputFromFile(string $fileName): array
{
    if (!file_exists($fileName)) {
        throw new Exception(sprintf('No input file with name: %s', $fileName));
    }

    $input = file($fileName, FILE_IGNORE_NEW_LINES);
    if (empty($input) || !is_array($input)) {
        throw new Exception(sprintf('Empty input file with name: %s', $fileName));
    }

    return array_map(function (string $row) {
        $seperated = explode(': ', $row);

        return [
            'total' => $seperated[0],
            'values' => explode(' ', $seperated[1])
        ];
    }, $input);

}
