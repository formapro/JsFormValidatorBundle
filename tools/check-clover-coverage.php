<?php

if (4 !== $argc) {
    fwrite(STDERR, "Usage: php tools/check-clover-coverage.php <clover.xml> <min-percent> <label>\n");
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
if (false === $xml || !isset($xml->project->metrics)) {
    fwrite(STDERR, sprintf("%s coverage report is not valid Clover XML: %s\n", $label, $file));
    exit(1);
}

$metrics = $xml->project->metrics;
$statements = (int) $metrics['statements'];
$coveredStatements = (int) $metrics['coveredstatements'];

if (0 === $statements) {
    fwrite(STDERR, sprintf("%s coverage report has no statements.\n", $label));
    exit(1);
}

$coverage = $coveredStatements / $statements * 100;
printf(
    "%s line coverage: %.2f%% (%d/%d), minimum: %.2f%%\n",
    $label,
    $coverage,
    $coveredStatements,
    $statements,
    $minimum
);

if ($coverage + 0.00001 < $minimum) {
    fwrite(STDERR, sprintf("%s line coverage is below the required minimum.\n", $label));
    exit(1);
}
