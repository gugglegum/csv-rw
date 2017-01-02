<?php

declare(strict_types=1);

namespace gugglegum\CsvRw;

class CsvWriter
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
     * Opens CSV file or URL/stream in write mode
     *
     * @param string $fileName    File name or URL/steam
     * @param bool   $withHeaders TRUE indicates that first line contains header names
     * @param array  $headers     OPTIONAL Headers to use if CSV without header-line or to override CSV headers
     * @return $this
     * @throws Exception
     */
    public function open(string $fileName, bool $withHeaders, array $headers = null)
    {
        if (!$fileHandle = @fopen($fileName, 'w')) {
            throw new Exception("Failed to open CSV file \"{$fileName}\" for writing");
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
        fclose($this->getValidFileHandle());
        $this->unAssign();
    }

    /**
     * Assigns existing file handle (resource) to read CSV data from it. Can be used to read data from "STDIN".
     *
     * @param resource $fileHandle  Opened file handle
     * @param bool     $withHeaders TRUE indicates that first line contains header names
     * @param array    $headers     OPTIONAL Headers to use if CSV without header-line or to override CSV headers
     * @return $this
     * @throws Exception
     */
    public function assign($fileHandle, bool $withHeaders, array $headers = null)
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
        if ($this->withHeaders) {
            if (empty($this->headers)) {
                throw new Exception("The headers must be defined for CSV file with headers");
            }
            $this->write($this->headers);
        }
        $this->isInitialized = true;
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
     * @return CsvWriter
     */
    public function setCsvFormat(CsvFormat $csvFormat): CsvWriter
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
        return $this->headers;
    }

    /**
     * Sets a names of columns
     *
     * @param array $headers
     * @return CsvWriter
     */
    public function setHeaders(array $headers): CsvWriter
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Writes a CSV row to file (or stream)
     *
     * If headers for CSV are defined passed array must be associative array where keys are header names. The amount of
     * array elements must be equal to amount of headers. If headers are not defined the array must be ordered
     * (contain keys 0, 1, 2, ...). Amount of elements must be the same for all rows.
     *
     * @param array $row Associative or ordered array with data of row to write in CSV
     * @throws Exception
     */
    public function writeRow(array $row)
    {
        if (!$this->isInitialized) {
            $this->init();
        }
        if (empty($row)) {
            throw new Exception('Attempt to write empty row in CSV file');
        }
        if ($this->headers === null) {
            $this->headers = range(0, count($row) - 1);
        }

        if ($unexpected = array_diff(array_keys($row), $this->headers)) {
            throw new Exception('Passed data for CSV contains unexpected field(s): "' . implode('", "', $unexpected) . '" (expected: "' . implode('", "', $this->headers) . '")');
        }

        // $fields is the ordered array where values sorted as in headers
        $fields = [];
        $missing = [];
        foreach ($this->headers as $key) {
            if (!array_key_exists($key, $row)) {
                $missing[] = $key;
            }
            $fields[] = $row[$key];
        }
        if (!empty($missing)) {
            throw new Exception('Passed data for CSV missing field(s): "' . implode('", "', $missing) . '"');
        }
        $this->write($fields);
    }

    private function write(array $fields)
    {
        $this->lineNumber++;
        if (fputcsv($this->getValidFileHandle(), $fields, $this->csvFormat->getDelimiter(), $this->csvFormat->getEnclosure(), $this->csvFormat->getEscape()) === false) {
            throw new Exception("Failed to write CSV row at line {$this->lineNumber}");
        }
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
    
    /**
     * Returns valid file handle CSV reader associated with or raises exception otherwise.
     *
     * @return resource
     * @throws Exception
     */
    private function getValidFileHandle()
    {
        if (!$this->fileHandle) {
            throw new Exception("CSV reader not associated with any file or stream");
        }
        if (!is_resource($this->fileHandle)) {
            throw new Exception("CSV reader associated with not valid file handle");
        }
        return $this->fileHandle;
    }
}
