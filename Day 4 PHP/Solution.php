<?php
$inputFileName = 'input.txt';
$input = getInputFromFile($inputFileName);

$part1Counter = 0;
$part2Counter = 0;
foreach ($input as $index => $line) {
    foreach (array_keys($line, 'X') as $xLine) {
        $part1Counter += countValidValues(getAllDirections($input, $xLine, $index, 4), 'XMAS');
    }

    foreach (array_keys($line, 'A') as $aLine) {
        $validValues = countValidValues(getPart2Directions($input, $aLine, $index), 'MAS', 'SAM');
        if ($validValues === 2) {
            $part2Counter++;
        }
    }
}

echo sprintf('XMAS Appearance Count (Part 1): %s', $part1Counter);
echo PHP_EOL;
echo sprintf('X-MAS Appearance Count (Part 2): %s', $part2Counter);

function getAllDirections(array $input, int $xAxis, int $yAxis, int $distance): array
{
    $output = [];
    foreach (range( 0, $distance - 1) as $delta) {
        $output['up'][$delta] = getValueAtIndex($input, $xAxis, $yAxis + $delta);
        $output['down'][$delta] = getValueAtIndex($input, $xAxis, $yAxis - $delta);
        $output['left'][$delta] = getValueAtIndex($input, $xAxis - $delta, $yAxis);
        $output['right'][$delta] = getValueAtIndex($input, $xAxis + $delta, $yAxis);

        $output['upLeft'][$delta] = getValueAtIndex($input, $xAxis - $delta, $yAxis + $delta);
        $output['upRight'][$delta] = getValueAtIndex($input, $xAxis + $delta, $yAxis + $delta);
        $output['downLeft'][$delta] = getValueAtIndex($input, $xAxis - $delta, $yAxis - $delta);
        $output['downRight'][$delta] = getValueAtIndex($input, $xAxis + $delta, $yAxis - $delta);

    }

    return array_map('implode', $output);
}

function getPart2Directions(array $input, int $xAxis, int $yAxis): array
{
    $output = [];
    foreach (range(-1, 1) as $delta) {
        $output['back'][$delta] = getValueAtIndex($input, $xAxis + $delta, $yAxis + $delta);
        $output['forward'][$delta] = getValueAtIndex($input, $xAxis + $delta, $yAxis - $delta);
    }

    return array_map('implode', $output);
}

function countValidValues(array $output, string ...$validValues): int
{
    $validCounter = 0;
    foreach ($output as $word) {
        foreach ($validValues as $validValue) {
            if ($word !== $validValue) {
                continue;
            }
            $validCounter++;
            
            continue 2;
        }
    }
    
    return $validCounter;
}

function getValueAtIndex(array $input, int $xAxis, int $yAxis ): ?string
{
    return $input[$yAxis][$xAxis] ?? null;
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

    return array_map(fn(string $line) => str_split($line), $input);
}
