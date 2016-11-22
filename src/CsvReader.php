<?php

declare(strict_types=1);

namespace gugglegum\CsvRw;

use Iterator;

class CsvReader implements Iterator
{
    /**
     * Auxiliary constant means "with headers" to be passed into `open()` and `assign()` methods to improve code
     * readability
     */
    const WITH_HEADERS = true;

    /**
     * Auxiliary constant means "without headers" to be passed into `open()` and `assign()` methods to improve code
     * readability
     */
    const WITHOUT_HEADERS = false;

    /**
     * An object representing CSV format options
     *
     * @var CsvFormat
     */
    private $csvFormat;

    /**
     * Indicates whether opened CSV file contains names of columns in first line or not
     *
     * @var bool
     */
    private $withHeaders;

    /**
     * A names of columns read from first line or set manually
     *
     * @var null|array
     */
    private $headers;

    /**
     * Current line number in CSV file
     *
     * @var int
     */
    private $lineNumber;

    /**
     * Opened CSV file handle
     *
     * @var resource
     */
    private $fileHandle;

    /**
     * Current number of row starting from 0
     *
     * @var int
     */
    private $currentIndex;

    /**
     * Current row array
     *
     * @var null|array
     */
    private $currentRow;

    /**
     * Indicates whether reader initialized or not
     *
     * @var bool
     */
    private $isInitialized;

    /**
     * CsvReader constructor
     *
     * @param CsvFormat $csvFormat
     */
    public function __construct(CsvFormat $csvFormat)
    {
        $this->csvFormat = $csvFormat;
    }

    /**
     * Opens CSV file or URL/stream in read mode
     *
     * @param string $fileName    File name or URL/steam
     * @param bool   $withHeaders TRUE indicates that first line contains header names
     * @param array  $headers     OPTIONAL Headers to use if CSV without header-line or to override CSV headers
     * @return CsvReader
     * @throws Exception
     */
    public function open(string $fileName, bool $withHeaders, array $headers = null): CsvReader
    {
        if (!$fileHandle = @fopen($fileName, 'r')) {
            throw new Exception("Failed to open CSV file \"{$fileName}\" for reading");
        }
        $this->assign($fileHandle, $withHeaders, $headers);
        return $this;
    }

    /**
     * Closes CSV file or URL/stream and resets internal state. This method should be called after `open()` method if
     * you no more want to read.
     */
    public function close()
    {
        fclose($this->fileHandle);
        $this->unAssign();
    }

    /**
     * Assigns existing file handle (resource) to read CSV data from it. Can be used to read data from "STDIN".
     *
     * @param resource $fileHandle  Opened file handle
     * @param bool     $withHeaders TRUE indicates that first line contains header names
     * @param array    $headers     OPTIONAL Headers to use if CSV without header-line or to override CSV headers
     * @return CsvReader
     * @throws Exception
     */
    public function assign($fileHandle, bool $withHeaders, array $headers = null): CsvReader
    {
        $this->fileHandle = $fileHandle;
        $this->withHeaders = $withHeaders;
        $this->headers = $headers;
        $this->isInitialized = false;
        return $this;
    }

    /**
     * Un-assigns file handle from CSV reader. This method should be called after `assign()` method if you no more want
     * to read.
     */
    public function unAssign()
    {
        $this->fileHandle = null;
        $this->withHeaders = null;
        $this->headers = null;
        $this->isInitialized = false;
    }

    /**
     * Initializes internal state of newly opened or assigned file
     *
     * @throws Exception
     */
    private function init()
    {
        $this->lineNumber = 0;
        $this->currentIndex = -1;
        $this->currentRow = null;

        if ($this->withHeaders) {
            if (!$row = $this->readRow()) {
                throw new Exception("Can't read headers from CSV file");
            }
            if ($this->headers === null) {
                $this->headers = $row;
            }
        }
        $this->isInitialized = true;
        $this->next();
    }

    /**
     * Returns an object representing CSV format options
     *
     * @return CsvFormat
     */
    public function getCsvFormat(): CsvFormat
    {
        return $this->csvFormat;
    }

    /**
     * Sets an object representing CSV format options
     *
     * @param CsvFormat $csvFormat
     * @return CsvReader
     */
    public function setCsvFormat(CsvFormat $csvFormat): CsvReader
    {
        $this->csvFormat = $csvFormat;
        return $this;
    }

    /**
     * Returns current line number
     *
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * Returns a names of columns which was read from first line or set manually
     *
     * @return null|array
     * @throws Exception
     */
    public function getHeaders()
    {
        if (!$this->isInitialized) {
            $this->init();
        }
        return $this->headers;
    }

    /**
     * Sets a names of columns
     *
     * @param array $headers
     * @return CsvReader
     */
    public function setHeaders(array $headers): CsvReader
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Returns current row if it exists, null otherwise. When non-empty CSV file just opened or assigned this method
     * returns its first row. If column headers are set the row represents an associative array, ordered array otherwise.
     *
     * @return array|null
     * @throws Exception
     */
    public function current()
    {
        if (!$this->isInitialized) {
            $this->init();
        }
        return $this->currentRow;
    }

    /**
     * Returns a number of current row (starting from 0). When CSV file just opened or assigned this method returns 0
     * (no matter is CSV file empty or not).
     *
     * @return int|null
     * @throws Exception
     */
    public function key()
    {
        if (!$this->isInitialized) {
            $this->init();
        }
        return $this->currentIndex;
    }

    /**
     * Returns TRUE if current row is valid. It returns FALSE if and only if `key()` pointing to end of file.
     *
     * @return bool
     * @throws Exception
     */
    public function valid()
    {
        if (!$this->isInitialized) {
            $this->init();
        }
        return $this->currentRow !== null;
    }

    /**
     * Reads a row from CSV file and updates current iterator state. This method should be used to iterate CSV file
     * rows.
     *
     * @throws Exception
     */
    public function next()
    {
        if (!$this->isInitialized) {
            $this->init();
        }
        $row = $this->readRow();
        if ($row !== false) {
            if ($this->headers === null) {
                $this->headers = range(0, count($row) - 1);
            }
            if (count($this->headers) !== count($row)) {
                throw new Exception("Invalid amount of columns in line {$this->lineNumber} (expected "
                    . count($this->headers).", got " . count($row) . ")");
            }
            $this->currentRow = array_combine($this->headers, $row);
        } else {
            $this->currentRow = null;
        }
        $this->currentIndex++;
    }

    /**
     * Returns all rows from CSV file
     *
     * @return array
     */
    public function getAllRows()
    {
        $rows = [];
        foreach ($this as $row) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Reads a row from CSV file
     *
     * @return false|array
     * @throws Exception
     */
    private function readRow()
    {
        if (feof($this->fileHandle)) {
            return false;
        }
        $this->lineNumber++;
        if (($row = fgetcsv($this->fileHandle, 0, $this->csvFormat->getDelimiter(), $this->csvFormat->getEnclosure(), $this->csvFormat->getEscape())) === false) {
            return false;
        }
        return $row;
    }

    /**
     * Returns file position to the beginning of CSV file
     *
     * @throws Exception
     */
    public function rewind()
    {
        rewind($this->fileHandle);
        $this->init();
    }

    /**
     * Returns file handle CSV Reader associated with. You may use this method to make something with file handle.
     * But in most cases you don't need this.
     *
     * @return null|resource
     */
    public function getFileHandle()
    {
        return $this->fileHandle;
    }
}
