<?php
$inputFileName = 'input.txt';
$input = getInputFromFile($inputFileName);

$part1Total = blinkAtStones($input, 25);
$part2Total = improvedBlinkAtStones($input, 75);

echo sprintf('No of Stones after blinking 25 times at them (Part 1): %s', $part1Total);
echo PHP_EOL;
echo sprintf('No of Stones after blinking 75 times at them(Part 2): %s', $part2Total);

function improvedBlinkAtStones(array $stones, int $maxBlinks): int
{
    $cache = [];
    foreach ($stones as $stone) {
        recursiveFind($stone, $cache, 0, $maxBlinks);
    }

    return array_reduce($cache[0], fn($sum, $stoneTotal) => $sum += $stoneTotal);
}

function recursiveFind(int $stone, array &$cache, int $depth, int $max): int
{
    if ($depth === $max) {
        return 1;
    }

    if (isset($cache[$depth][$stone])) {
        return $cache[$depth][$stone];
    }

    if ($stone === 0) {
        $cache[$depth][$stone] = recursiveFind(1, $cache, $depth + 1, $max);

        return $cache[$depth][$stone];
    }

    $length = strlen((string)$stone);
    if ($length % 2 === 0) {
        $splitVals = str_split((string)$stone, $length / 2);
        $left = recursiveFind((int)$splitVals[0], $cache, $depth + 1, $max);
        $right = recursiveFind((int)$splitVals[1], $cache, $depth + 1, $max);

        $cache[$depth][$stone] = $left + $right;

        return $left + $right;
    }
    
    $cache[$depth][$stone] = recursiveFind($stone * 2024, $cache, $depth + 1, $max);

    return $cache[$depth][$stone];
}


function blinkAtStones(array $input, int $maxBlinks): int
{
    foreach (range(1, $maxBlinks) as $counter) {
        $temp = [];
        foreach ($input as $stone) {
            if ($stone === 0) {
                $temp[] = 1;

                continue;
            }

            $length = strlen((string)$stone);
            if ($length % 2 === 0) {
                $splitVals = str_split((string)$stone, $length / 2);
                $temp[] = (int)$splitVals[0];
                $temp[] = (int)$splitVals[1];

                continue;
            }

            $temp[] = $stone * 2024;
        }

        $input = $temp;
    }

    return count($input);
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

    return array_map('intval', explode(' ', $input[0]));
}

