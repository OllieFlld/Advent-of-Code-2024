import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.util.Arrays;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class Main {
    public static void main(String[] args) {
        String filename = "input.txt";
        String input = null;
        try {
            input = getInputFromFile(filename);
        } catch (IOException e) {
            throw new RuntimeException(e);
        }

        String part1Pattern = "(?<=mul\\()\\d*,\\d*(?=\\))";
        int part1Result = multipleAndSumInstructions(getMatches(input, part1Pattern));

        String part2Pattern = "(don't(.*?)(do\\(\\)))|(don't\\(\\))";
        int part2Result = multipleAndSumInstructions(getMatches(input.replaceAll(part2Pattern, ""), part1Pattern));

        System.out.println("Instructions (Part 1): " + part1Result);
        System.out.println("Just Enabled Instructions (Part 2): " + part2Result);

    }

    public static int multipleAndSumInstructions(Matcher matches) {
        return matches
                .results()
                .map((matchResult -> multiplyMatchResult(matchResult.group())))
                .reduce(0, Integer::sum);
    }

    public static Matcher getMatches(String input, String regex) {
        Pattern pattern = Pattern.compile(regex);

        return pattern.matcher(input);
    }

    public static String getInputFromFile(String fileName) throws IOException {
        return Files
                .readString(Path.of(fileName))
                .replace("\r", "")
                .replace("\n", "");
    }

    public static int multiplyMatchResult(String matchResult) {
        String[] split = matchResult.split(",");

        return Arrays.stream(split).mapToInt(Integer::valueOf).reduce(1, (a, b) -> a * b);
    }
}