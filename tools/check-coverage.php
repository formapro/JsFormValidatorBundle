<?php

if (4 !== $argc) {
    fwrite(STDERR, "Usage: php tools/check-coverage.php <coverage.xml> <min-percent> <label>\n");
    exit(2);
}

$file = $argv[1];
$minimum = (float) $argv[2];
$label = $argv[3];

if (!is_file($file)) {
    fwrite(STDERR, sprintf("%s coverage report not found: %s\n", $label, $file));
    exit(1);
}

$xml = simplexml_load_file($file);
if (false === $xml) {
    fwrite(STDERR, sprintf("%s coverage report is not valid XML: %s\n", $label, $file));
    exit(1);
}

[$coveredLines, $validLines, $coverage] = getCoverageMetrics($xml, $label);
printf(
    "%s line coverage: %.2f%% (%d/%d), minimum: %.2f%%\n",
    $label,
    $coverage,
    $coveredLines,
    $validLines,
    $minimum
);

if ($coverage + 0.00001 < $minimum) {
    fwrite(STDERR, sprintf("%s line coverage is below the required minimum.\n", $label));
    exit(1);
}

/**
 * @return array{int, int, float}
 */
function getCoverageMetrics(SimpleXMLElement $xml, string $label): array
{
    if ('coverage' !== $xml->getName()) {
        fwrite(STDERR, sprintf("%s coverage report is not Cobertura XML.\n", $label));
        exit(1);
    }

    $validLines = (int) $xml['lines-valid'];
    $coveredLines = (int) $xml['lines-covered'];

    if (0 === $validLines && isset($xml['line-rate'])) {
        $coverage = (float) $xml['line-rate'] * 100;

        return array(0, 0, $coverage);
    }

    if (0 === $validLines) {
        fwrite(STDERR, sprintf("%s coverage report has no lines.\n", $label));
        exit(1);
    }

    return array($coveredLines, $validLines, $coveredLines / $validLines * 100);
}
