<?php

use gugglegum\CsvRw\CsvFormat;
use gugglegum\CsvRw\CsvReader;
use gugglegum\CsvRw\Exception;

require_once __DIR__ . '/../../src/CsvFormat.php';
require_once __DIR__ . '/../../src/CsvReader.php';
require_once __DIR__ . '/../../src/Exception.php';

$options = [
    'delimiter' => ',',
    'enclosure' => '"',
    'escape' => '\\',
];

$csv = new CsvReader(new CsvFormat($options));

if (!$handle = @fopen(__DIR__ . '/../samples/sample-10.with-header.csv', 'r')) {
    echo "ERROR: Failed to open CSV file\n";
    exit;
}

try{
    $csv->assign($handle, CsvReader::WITH_HEADERS);

    while ($csv->valid()) {
        $row = $csv->current();
        echo "{$csv->key()}: Line {$csv->getLineNumber()}\n";
        var_dump($row);
        $csv->next();
    }

    $csv->close();

} catch (Exception $e) {
    echo "ERROR: {$e->getMessage()}\n";
    exit;
}
