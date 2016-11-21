# CSV Reader & Writer

This is easy to use Composer component to read and write CSV files in very convenient way. The CSV Reader is actually
an OO-wrapper on `fgetcsv()` built-in PHP function. It adds exceptions on errors, implements `Iterator` interface.
You can open a CSV file and then iterate CSV reader in `foreach` statement like array. It supports headers of columns,
so you may handle with associative data arrays. Headers may be read from first line of CSV file or be set manually.
Or be read from first line and then changed by some reflection function or whatever you want.

Instead of opening you also can assign already opened file handle or stream. This components adds validation of data 
consistency. For example, it throws an exception if some row contains less or more columns than previous one. Therefore
you don't need to always check type of return values. Just use `try .. catch` statement and catch exceptions if 
necessary. Package uses strong typing for scalars came to us from PHP7.

## Usage

### Read CSV

```php
$csv = new \gugglegum\CsvRw\CsvReader(new \gugglegum\CsvRw\CsvFormat([
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

Sorry, only reader implemented at the moment.

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

### Add composer's autoloader

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
