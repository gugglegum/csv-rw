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

$underscoreToCamelCaseLowerFirstReflection = function(string $key) : string
{
    if (strlen($key) > 0) {
        $key = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
        $key[0] = strtolower($key[0]);
    }
    return $key;
};

$csv = new CsvReader(new CsvFormat($options));

try{
    $csv->open(__DIR__ . '/../samples/sample-10.with-header.csv', CsvReader::WITH_HEADERS);

    $csv->setHeaders(array_map($underscoreToCamelCaseLowerFirstReflection, $csv->getHeaders()));

    foreach ($csv as $index => $row) {
        echo "{$index}: Line {$csv->getLineNumber()}\n";
        var_dump($row);
    }

    $csv->close();

} catch (Exception $e) {
    echo "ERROR: {$e->getMessage()}\n";
    exit;
}
