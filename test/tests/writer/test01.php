<?php

use gugglegum\CsvRw\CsvFormat;
use gugglegum\CsvRw\CsvReader;
use gugglegum\CsvRw\CsvWriter;
use gugglegum\CsvRw\Exception;

require_once __DIR__ . '/../../../src/CsvFormat.php';
require_once __DIR__ . '/../../../src/CsvReader.php';
require_once __DIR__ . '/../../../src/CsvWriter.php';
require_once __DIR__ . '/../../../src/Exception.php';

$options = [
    'delimiter' => ',',
    'enclosure' => '"',
    'escape' => '\\',
];

$headers = ["firstName", "lastName", "companyName", "address", "city", "county", "state", "zip", "phone1", "phone2", "email", "web"];

$rows = (new CsvReader(new CsvFormat($options)))
    ->open(__DIR__ . '/../../samples/sample-10.with-header.csv', CsvReader::WITH_HEADERS)
    ->getAllRows();

$csv = new CsvWriter(new CsvFormat($options));
try{
    $csv->assign(STDOUT, CsvWriter::WITH_HEADERS, $headers);

    foreach ($rows as $row) {
        $csv->writeRow($row);
    }
    $csv->close();

} catch (Exception $e) {
    echo "ERROR: {$e->getMessage()}\n";
    exit;
}
