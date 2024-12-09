<?php

enum Direction: string
{
    case Up = '^';
    case Right = '>';
    case Down = 'v';
    case Left = '<';
}

$inputFileName = 'input.txt';
$map = getInputFromFile($inputFileName);

$guardChars = [Direction::Up, Direction::Right, Direction::Down, Direction::Left];

$guardLocation = findGuard($map, $guardChars);

$visitedLocations = getVisitedLocations($map, $guardLocation, $guardChars);
$uniqueVisitedLocations = getUniqueVisitedLocations($visitedLocations['visitedLocations']);
$countLoopsWithObstructions = getPart2ObstructionCount($map, $guardLocation, $guardChars, $uniqueVisitedLocations);

echo sprintf('Part 1 total: %s', count($uniqueVisitedLocations));
echo PHP_EOL;
echo sprintf('Part 2 total: %s', $countLoopsWithObstructions);


function getUniqueVisitedLocations(array $visitedLocations): array
{
    $output = [];
    foreach ($visitedLocations as $visitedLocation) {
        $key = sprintf('%s, %s', $visitedLocation->getX(), $visitedLocation->getY());
        if (isset($output[$key])) {
            continue;
        }

        $output[$key] = $visitedLocation;
    }

    return array_values($output);
}

function getVisitedLocations(array $map, Location $guardLocation, array $guardChars): array
{
    $output = ['exitedMap' => false, 'inLoop' => false, 'visitedLocations' => []];
    while (true) {
        $guard = $guardLocation->getDirection();

        $nextPosition = new Location($guardLocation->getX(), $guardLocation->getY(), $guard);

        if ($guard === Direction::Up) {
            $nextPosition->setX($nextPosition->getX() - 1);
        }
        if ($guard === Direction::Down) {
            $nextPosition->setX($nextPosition->getX() + 1);
        }
        if ($guard === Direction::Left) {
            $nextPosition->setY($nextPosition->getY() - 1);
        }
        if ($guard === Direction::Right) {
            $nextPosition->setY($nextPosition->getY() + 1);
        }
        if (in_array($nextPosition, $output['visitedLocations'])) {
            // Loop has occurred
            $output['visitedLocations'][] = $guardLocation;
            $output['inLoop'] = true;

            break;
        }
        
        $nextPositionContents = $map[$nextPosition->getX()][$nextPosition->getY()] ?? null;
        if ($nextPositionContents === null) {
            $map[$guardLocation->getX()][$guardLocation->getY()] = 'X';
            $output['visitedLocations'][] = $guardLocation;
            $output['exitedMap'] = true;

            break;
        }

        if ($nextPositionContents === '.' || $nextPositionContents === 'X') {
            $map[$guardLocation->getX()][$guardLocation->getY()] = 'X';
            $map[$nextPosition->getX()][$nextPosition->getY()] = $guard->value;
            $output['visitedLocations'][] = $guardLocation;
            $guardLocation = $nextPosition;

            continue;
        }
        
        $guardLocation->setDirection(rotateGuard($guard, $guardChars));
        $map[$guardLocation->getX()][$guardLocation->getY()] = rotateGuard($guard, $guardChars)->value;
    }

   return $output;
}

function getPart2ObstructionCount(array $map, Location $guardLocation, array $guardChars, array $visitedLocations): int
{
    $counter = 0;
    array_shift($visitedLocations);
    foreach ($visitedLocations as $visitedLocation) {
        $modifiedMap = $map;
        $modifiedMap[$visitedLocation->getX()][$visitedLocation->getY()] = '0';
        $withObstruction = getVisitedLocations($modifiedMap, clone $guardLocation, $guardChars);
        if ($withObstruction['inLoop'] === false) {
            continue;
        }
        $counter++;
    }

    return $counter;
}

function rotateGuard(Direction $guard, array $guardChars): Direction
{
    return $guardChars[array_search($guard, $guardChars) + 1] ?? $guardChars[0];
}

function findGuard(array $map, array $guardChars): Location
{
    foreach ($map as $index => $row) {
        foreach ($guardChars as $guardChar) {
            $search = array_search($guardChar->value, $row);
            if ($search !== false) {
                return new Location($index, $search, $guardChar);
            }
        }
    }

    throw new Exception('No Guard Found');
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

    return array_map(fn(string $row) => str_split($row), $input);
}

class Location
{
    private int $x;
    private int $y;
    private Direction $direction;

    public function __construct(int $x, int $y, Direction $direction)
    {
        $this->setX($x);
        $this->setY($y);
        $this->setDirection($direction);
    }

    public function getX(): int
    {
        return $this->x;
    }
    
    public function setX(int $x): void
    {
        $this->x = $x;
    }
    
    public function getY(): int
    {
        return $this->y;
    }

    /**
     * @param int $y
     */
    public function setY(int $y): void
    {
        $this->y = $y;
    }
    
    public function getDirection(): Direction
    {
        return $this->direction;
    }
    
    public function setDirection(Direction $direction): void
    {
        $this->direction = $direction;
    }
}

