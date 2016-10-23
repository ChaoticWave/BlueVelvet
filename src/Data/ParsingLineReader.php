<?php namespace ChaoticWave\BlueVelvet\Data;

use ChaoticWave\BlueVelvet\Enums\Delimiters;
use ChaoticWave\BlueVelvet\Enums\Escapes;
use ChaoticWave\BlueVelvet\Enums\Wrappers;
use ChaoticWave\BlueVelvet\Exceptions\ParsingException;

/**
 * A parsing data reader
 */
class ParsingLineReader extends LineReader
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @var string
     */
    protected $delimiter = Delimiters::COMMA;
    /**
     * @var string
     */
    protected $wrapper = Wrappers::DOUBLE_QUOTE;
    /**
     * @var int
     */
    protected $escape = Escapes::SLASHED;
    /**
     * @var array
     */
    protected $keys;
    /**
     * @var bool
     */
    protected $overrideKeys = false;
    /**
     * @var bool
     */
    protected $header = true;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * Constructor
     *
     * @param array|mixed $filename
     * @param null        $keys
     * @param string      $delimiter
     * @param string      $wrapper
     * @param int         $escape
     */
    public function __construct($filename, $keys = null, $delimiter = Delimiters::COMMA, $wrapper = Wrappers::DOUBLE_QUOTE, $escape = Escapes::SLASHED)
    {
        parent::__construct($filename);

        $this->keys = $keys;
        $this->delimiter = $delimiter;
        $this->wrapper = $wrapper;
        $this->escape = $escape;
    }

    /**
     * @return array|bool|mixed|null
     */
    public function current()
    {
        if (null !== $this->cursor) {
            return $this->cursor;
        }

        if (false === ($_line = $this->readLine())) {
            return null;
        }

        if (null === $this->keys) {
            return $this->cursor = $_line;
        }

        $this->cursor = [];

        reset($this->keys);

        foreach ($_line as $_column) {
            if (false === ($_key = each($this->keys))) {
                break;
            }

            $this->cursor[$_key['value']] = $_column;
        }

        return $this->cursor;
    }

    public function rewind()
    {
        parent::rewind();

        if ($this->header) {
            if (false === ($_header = $this->readLine(true))) {
                throw new ParsingException('Error reading header row from file: ' . $this->filename);
            }

            if (!$this->overrideKeys) {
                $this->keys = $_header;
            }
        }
    }

    /**
     * @param string $line
     *
     * @return array|bool
     */
    protected function parseLine($line)
    {
        $_result = str_getcsv($line,
            $this->delimiter,
            $this->wrapper,
            Escapes::SLASHED == $this->escape ? '\\' : Escapes::DOUBLED == $this->escape ? '"' : '');

        return parent::parseLine($_result);
    }

    /**
     * @param array $keys
     *
     * @return $this
     */
    public function setKeys($keys)
    {
        $this->keys = $keys;
        $this->overrideKeys = true;

        return $this;
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        if (!$this->bof) {
            $this->rewind();
        }

        return $this->keys;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     *
     * @return $this
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;

        return $this;
    }

    /**
     * @return string
     */
    public function getWrapper()
    {
        return $this->wrapper;
    }

    /**
     * @param string $wrapper
     *
     * @return $this
     */
    public function setWrapper($wrapper)
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return int
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * @param int $escape
     *
     * @return $this
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getOverrideKeys()
    {
        return $this->overrideKeys;
    }

    /**
     * @param boolean $overrideKeys
     *
     * @return $this
     */
    public function setOverrideKeys($overrideKeys)
    {
        $this->overrideKeys = $overrideKeys;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @param boolean $header
     *
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }
}