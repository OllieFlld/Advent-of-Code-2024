int[] firstLocationIds = {};
int[] secondLocationIds = {};

var sortedFirst = BubbleSort(firstLocationIds);
var sortedSecond = BubbleSort(secondLocationIds);

var solutionPart1 = SumLocationDifferences(sortedFirst, sortedSecond);
Console.WriteLine("Part 1 Solution: " + solutionPart1);

var solutionPart2 = CountTotalLocationOccurrences(sortedFirst, sortedSecond);
Console.WriteLine("Part 2 Solution: " + solutionPart2);

static int[] BubbleSort(int[] array)
{
    for (int i = 0; i < array.Length; i++)
    {
        for (int j = 0; j < array.Length - i - 1; j++)
        {
            if (array[j] > array[j + 1])
            {
                (array[j + 1], array[j]) = (array[j], array[j + 1]);
            }
        }
    }


    return array;
}


static int SumLocationDifferences(int[] sortedFirst, int[] sortedSecond)
{
    int output = 0;
    for (int i = 0; i < sortedFirst.Length; i++)
    {
        var first = sortedFirst[i];
        var second = sortedSecond[i];
    
        output += Math.Abs(sortedFirst[i] - sortedSecond[i]);
    }
    
    return output;
}

static int CountTotalLocationOccurrences(int[] sortedFirst, int[] sortedSecond)
{
    var totalCount = 0;
    foreach (var locationId in sortedFirst)
    {
        totalCount += CountLocationOccurrences(locationId, sortedSecond);
    }

    return totalCount;
}

static int CountLocationOccurrences(int locationId, int[] sortedSecond)
{
    var locationCounter = 0;
    foreach (var secondLocationId in sortedSecond)
    {
        if (locationId < secondLocationId)
        {
            break;
        }
        
        if (locationId == secondLocationId)
        {
            locationCounter++;
        }
    }

    return locationId * locationCounter;
}