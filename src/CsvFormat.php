<?php

namespace gugglegum\CsvRw;

/**
 * CSV Format
 *
 * Contains CSV format agreement options. Only low-level options needle to parse lines.
 *
 * @package gugglegum\CsvReader
 */
class CsvFormat
{
    /**
     * The delimiter parameter sets the field delimiter (one character only)
     *
     * @var string
     */
    private $delimiter;

    /**
     * The enclosure parameter sets the field enclosure character (one character only)
     *
     * @var string
     */
    private $enclosure;

    /**
     * The escape parameter sets the escape character (one character only)
     *
     * @var string
     */
    private $escape;

    /**
     * CsvOptions constructor
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        if ($options !== null) {
            $this->setFromArray($options);
        }
    }

    /**
     * @param array $options
     */
    public function setFromArray(array $options)
    {
        foreach ($options as $option => $value) {
            switch ($option) {
                case 'delimiter' :
                    $this->setDelimiter($value);
                    break;
                case 'enclosure' :
                    $this->setEnclosure($value);
                    break;
                case 'escape' :
                    $this->setEscape($value);
                    break;
                default :
                    throw new Exception("Unknown CSV option '{$option}'");
            }
        }
    }

    /**
     * @return string
     */
    public function getDelimiter(): string
    {
        if ($this->delimiter === null) {
            throw new Exception("The `delimiter' option not set");
        }
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     * @return CsvFormat
     */
    public function setDelimiter(string $delimiter): CsvFormat
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnclosure(): string
    {
        if ($this->enclosure === null) {
            throw new Exception("The `enclosure' option not set");
        }
        return $this->enclosure;
    }

    /**
     * @param string $enclosure
     * @return CsvFormat
     */
    public function setEnclosure(string $enclosure): CsvFormat
    {
        $this->enclosure = $enclosure;
        return $this;
    }

    /**
     * @return string
     */
    public function getEscape(): string
    {
        if ($this->escape === null) {
            throw new Exception("The `escape' option not set");
        }
        return $this->escape;
    }

    /**
     * @param string $escape
     * @return CsvFormat
     */
    public function setEscape(string $escape): CsvFormat
    {
        $this->escape = $escape;
        return $this;
    }
}
