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

$rows = ($csvReader = new CsvReader($format))
    ->open(__DIR__ . '/../../samples/sample-10.with-header.csv', CsvReader::WITH_HEADERS)
    ->getAllRows();

$csvWriter = new CsvWriter($format);
try{
    $csvWriter->assign(STDOUT, CsvWriter::WITH_HEADERS, $csvReader->getHeaders());

    foreach ($rows as $row) {
        $csvWriter->writeRow($row);
    }
    $csvWriter->unAssign();

} catch (Exception $e) {
    echo "ERROR: {$e->getMessage()}\n";
    exit;
}
