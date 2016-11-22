<?php

use gugglegum\CsvRw\CsvFormat;
use gugglegum\CsvRw\CsvReader;
use gugglegum\CsvRw\CsvWriter;
use gugglegum\CsvRw\Exception;

require_once __DIR__ . '/../../../src/CsvFormat.php';
require_once __DIR__ . '/../../../src/CsvReader.php';
require_once __DIR__ . '/../../../src/CsvWriter.php';
require_once __DIR__ . '/../../../src/Exception.php';

$format = new CsvFormat([
    'delimiter' => ',',
    'enclosure' => '"',
    'escape' => '\\',
]);

$rows = (new CsvReader($format))
    ->open(__DIR__ . '/../../samples/sample-10.without-header.csv', CsvReader::WITHOUT_HEADERS)
    ->getAllRows();

$csv = new CsvWriter($format);
try{
    $csv->assign(STDOUT, CsvWriter::WITHOUT_HEADERS);

    foreach ($rows as $row) {
        $csv->writeRow($row);
    }
    $csv->unAssign();

} catch (Exception $e) {
    echo "ERROR: {$e->getMessage()}\n";
    exit;
}
