int[,] directions =
{
    { -1, 0 },
    { 1, 0 },
    { 0, -1 },
    { 0, 1 }
};

const string inputFileName = "input.txt";
var input = getInputFromFile(inputFileName);

var totalPaths = 0;
var totalTrailHeadRatings = 0;
for (var row = 0; row < input.GetLength(0); row++)
{
    for (int column = 0; column < input.GetLength(1); column++)
    {
        if (input[row, column] != 0)
        {
            continue;
        }

        var found = new List<Location>();
        CheckLocation(input, new Location(row, column), directions, found);
        totalTrailHeadRatings += found.Count;
        totalPaths += found.Distinct().Count();
    }
}

Console.WriteLine("Total Paths (Part 1): " + totalPaths);
Console.WriteLine("Total Trail Head Rating (Part 2): " + totalTrailHeadRatings);

static void CheckLocation(int[,] input, Location previous, int[,] directions, List<Location> foundTops)
{
    int value = input[previous.X, previous.Y];

    for (int direction = 0; direction < directions.GetLength(0); direction++)
    {
        var xToCheck = previous.X + directions[direction, 0];
        var yToCheck = previous.Y + directions[direction, 1];

        if (xToCheck >= 0 && xToCheck < input.GetLength(0) && yToCheck >= 0 && yToCheck < input.GetLength(1)) 
        {
            var nextVal = input[xToCheck, yToCheck];
            if (nextVal - value != 1)
            {
                continue;
            }

            if (nextVal == 9)
            {
                foundTops.Add(new Location(xToCheck, yToCheck));
            }

            CheckLocation(input, new Location(xToCheck, yToCheck), directions, foundTops);
        }
    }
}

static int[,] getInputFromFile(string fileName)
{
    var input = File.ReadAllText(fileName);
    var lines = input.Split('\n');

    var output = new int[lines.Length, lines[0].Length];
    for (int x = 0; x < lines.Length; x++)
    {
        var line = lines[x].ToCharArray();
        for (int y = 0; y < line.Length; y++)
        {
            output[x, y] = line[y] - '0';
        }
    }

    return output;
}

class Location
{
    public int X { get; }
    public int Y { get; }

    public Location(int x, int y)
    {
        this.X = x;
        this.Y = y;
    }

    protected bool Equals(Location other)
    {
        return X == other.X && Y == other.Y;
    }

    public override bool Equals(object? obj)
    {
        if (obj is null) return false;
        if (ReferenceEquals(this, obj)) return true;
        if (obj.GetType() != GetType()) return false;
        return Equals((Location)obj);
    }

    public override int GetHashCode()
    {
        return HashCode.Combine(X, Y);
    }
}

