import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

class Main {
    public static void main(String[] args) throws IOException {
        String inputFileName = "input.txt";
        char[][] input = getInputFromFile(inputFileName);
        Map<Character, List<Location>> antennas = getAntennas(input);

        List<Location> part1Antinodes = getAntinodes(antennas, input.length - 1, input[0].length - 1, false);
        List<Location> part2Antinodes = getAntinodes(antennas, input.length - 1, input[0].length - 1, true);

        System.out.println("Total Antinodes (Part 1): " + part1Antinodes.size());
        System.out.println("Total Antinodes with Harmonics (Part 2): " + part2Antinodes.size());
    }

    public static Map<Character, List<Location>> getAntennas(char[][] input) {
        Map<Character, List<Location>> antennas = new HashMap<>();
        for (int x = 0; x < input.length; x++) {
            for (int y = 0; y < input[x].length; y++) {
                char value = input[x][y];
                if (value == '.') {
                    continue;
                }
                if (!antennas.containsKey(value)) {
                    antennas.put(value, new ArrayList<>());
                }
                antennas.get(value).add(new Location(x, y));
            }
        }

        return antennas;
    }

    public static List<Location> getAntinodes(Map<Character, List<Location>> antennas, int xMax, int yMax, boolean enableHarmonics) {
        List<Location> antinodes = new ArrayList<>();
        for (Map.Entry<Character, List<Location>> entry : antennas.entrySet()) {
            entry.getValue().forEach((location) -> entry.getValue().forEach((location2) -> {
                if (location.equals(location2)) {
                    return;
                }

                int xDistance = location.getX() - location2.getX();
                int yDistance = location.getY() - location2.getY();

                int counter = enableHarmonics ? 0 : 1;
                while (true) {
                    int xPosition = location.getX() + (xDistance * counter);
                    int yPosition = location.getY() + (yDistance * counter);
                    if (xPosition > xMax || xPosition < 0) {
                        break;
                    }

                    if (yPosition > yMax || yPosition < 0) {
                        break;
                    }
                    antinodes.add(new Location(xPosition, yPosition));

                    if (!enableHarmonics) {
                        break;
                    }
                    counter++;
                }
            }));
        }

        return antinodes.stream().distinct().toList();
    }

    public static char[][] getInputFromFile(String fileName) throws IOException {
        return Files.newBufferedReader(Path.of(fileName)).lines().map(line -> line.toCharArray()).toArray(char[][]::new);
    }
}

class Location {
    private int x;
    private int y;

    public Location(int x, int y) {
        this.x = x;
        this.y = y;
    }

    public int getX() {
        return x;
    }

    public void setX(int x) {
        this.x = x;
    }

    public int getY() {
        return y;
    }

    public void setY(int y) {
        this.y = y;
    }

    @Override
    public String toString() {
        return getX() + ", " + getY();
    }

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (o == null || getClass() != o.getClass()) return false;

        Location location = (Location) o;

        if (x != location.x) return false;
        if (y != location.y) return false;

        return true;
    }

    @Override
    public int hashCode() {
        int result = x;
        result = 31 * result + y;
        return result;
    }
}
