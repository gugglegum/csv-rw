# CSV Reader & Writer

This is easy to use Composer package to read and write CSV files in very convenient way. The CSV Reader is actually
an OO-wrapper on the `fgetcsv()` built-in PHP function. It adds exceptions on errors and implements `Iterator` interface.
You can iterate CSV reader in `foreach` statement like array. It supports headers of columns,
so you may handle with associative data arrays. Headers may be read from first line of CSV file or be set manually.
Or be read from first line and then changed by some reflection function or whatever you want.

Instead of opening you also can assign already opened file handle or stream. This package contains validation of data 
consistency. For example, it throws an exception if some row contains less or more columns than previous one. Therefore
you don't need to always check type of return values. Just use `try .. catch` statement and catch exceptions if 
necessary. The package uses strong typing for scalars came to us from PHP7.

## Usage

See more examples of usage in `/test` folder.

### Read CSV

```php
use \gugglegum\CsvRw\CsvReader;
use \gugglegum\CsvRw\CsvFormat;

$csv = new CsvReader(new CsvFormat([
    'delimiter' => ',',
    'enclosure' => '"',
    'escape' => '\\',
]));
$csv->open('input.csv', CsvReader::WITH_HEADERS);

foreach ($csv as $row) {
    var_dump($row);
}

$csv->close();
```

### Write CSV

```php
use gugglegum\CsvRw\CsvFormat;
use gugglegum\CsvRw\CsvWriter;

$headers = ['id', 'firstName', 'lastName'];

$rows = [
    [
        'id' => 1,
        'firstName' => 'John',
        'lastName' => 'Smith',
    ],
];

$csv = new CsvWriter(new CsvFormat([
   'delimiter' => ',',
   'enclosure' => '"',
   'escape' => '\\',
]));

$csv->open('output.csv', CsvWriter::WITH_HEADERS, $headers);

foreach ($rows as $row) {
    $csv->writeRow($row);
}

$csv->close();
```

## Installation

This library is available as composer package. To start using composer in your project follow these step

### Install Composer

```
curl -s http://getcomposer.org/installer | php
mv ./composer.phar ~/bin/composer # or /usr/local/bin/composer
```

### Install this package

```
composer require gugglegum/csv-rw
```

### Add composer's autoloader to you code

```
require 'vendor/autoload.php';
```

Now you can use CSV reader & writer in your code.

## Troubleshooting

If you have troubles with MAC's line-endings `\r`, you may turn on PHP option `auto_detect_line_endings`:

> When turned on, PHP will examine the data read by fgets() and file() to see if it is using Unix, MS-Dos or Macintosh 
> line-ending conventions.

It may be turned on from you PHP code. Just add this before reading MAC files:
```php
ini_set('auto_detect_line_endings', true);
```
