<?php

$rulesFileName = 'rules.txt';
$inputFileName = 'input.txt';
$rules = getInputFromFile($rulesFileName, '|');
$input = getInputFromFile($inputFileName, ',');

$sortingRules = getRulesForSorting($rules);

$validLines = [];
$invalidSortedLines = [];
foreach ($input as $line) {
    $sortedLine = sortLine($line, $sortingRules);
    if ($line === $sortedLine) {
        $validLines[] = $line;

        continue;
    }

    $invalidSortedLines[] = $sortedLine;
}

echo sprintf('Total of Correctly Sorted Lines (Part 1): %s', calculateLinesTotal($validLines));
echo PHP_EOL;
echo sprintf('Total of Incorrectly but Now Sorted Lines (Part 2): %s', calculateLinesTotal($invalidSortedLines));

function getRulesForSorting(array $rules): array
{
    $allValues = array_unique(array_merge(array_column($rules, 0), array_column($rules, 1)));

    $sortedValues = [];
    foreach ($allValues as $value) {
        $sortedValues[$value] = [
            'before' => [],
            'after' => []
        ];
        foreach ($rules as $rule) {
            if ($rule[0] === $value) {
                $sortedValues[$value]['before'][] = $rule[1];
            }

            if ($rule[1] === $value) {
                $sortedValues[$value]['after'][] = $rule[0];
            }
        }
    }

    return $sortedValues;
}

function sortLine(array $line, array $sortingRules): array
{
    usort($line, function ($a, $b) use ($sortingRules) {
        $linesRules = $sortingRules[$a];
        if (in_array($b, $linesRules['before'])) {
            return -1;
        }

        if (in_array($b, $linesRules['after'])) {
            return 1;
        }

        return 0;
    });

    return $line;
}

function calculateLinesTotal(array $input): int
{
    $total = 0;
    foreach ($input as $line) {
        $total += getMiddleElement($line);
    }

    return $total;
}

function getMiddleElement(array $input): int
{
    return $input[(int)(count($input) / 2)];
}

function getInputFromFile(string $fileName, string $delimiter): array
{
    if (!file_exists($fileName)) {
        throw new Exception(sprintf('No input file with name: %s', $fileName));
    }

    $input = file($fileName, FILE_IGNORE_NEW_LINES);
    if (empty($input) || !is_array($input)) {
        throw new Exception(sprintf('Empty input file with name: %s', $fileName));
    }

    return array_map(fn($row) => explode($delimiter, $row), $input);
}
