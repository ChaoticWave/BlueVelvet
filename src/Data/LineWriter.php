<?php namespace ChaoticWave\BlueVelvet\Data;

use ChaoticWave\BlueVelvet\Contracts\WritesRowData;
use ChaoticWave\BlueVelvet\Enums\Breaks;
use ChaoticWave\BlueVelvet\Enums\Delimiters;
use ChaoticWave\BlueVelvet\Enums\Escapes;
use ChaoticWave\BlueVelvet\Enums\Wrappers;
use ChaoticWave\BlueVelvet\Exceptions\FileException;

/**
 * Tabular data writer
 */
class LineWriter extends ParsingLineReader implements WritesRowData
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @var int
     */
    protected $rowsOut = 0;
    /**
     * @var int
     */
    protected $linesOut = 0;
    /**
     * @var string|null
     */
    protected $nullValue = null;
    /**
     * @var string
     */
    protected $lineBreak = Breaks::LINUX;
    /**
     * @var bool
     */
    protected $autoWriteHeader = true;
    /**
     * @var bool
     */
    protected $appendEOL = true;
    /**
     * @var bool
     */
    protected $wrapWhitespace = false;
    /**
     * @var bool
     */
    protected $lazyWrap = false;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /** @inheritdoc */
    public function __construct($filename, $keys = null, $delimiter = Delimiters::COMMA, $wrapper = Wrappers::DOUBLE_QUOTE, $escape = Escapes::SLASHED)
    {
        parent::__construct($filename, $keys, $delimiter, $wrapper, $escape);

        if (false === ($this->resource = fopen($this->filename, 'w'))) {
            throw new FileException('Cannot open file "' . $this->filename . '" for writing.');
        }
    }

    /**
     * Choose your destructor!
     */
    public function __destruct()
    {
        if (is_resource($this->resource)) {
            $this->writeHeader(true);
            @fclose($this->resource);
            $this->resource = null;
        }
    }

    /** @inheritdoc */
    public function writeRow($data = [])
    {
        $this->writeHeader(true);

        if (empty($this->keys)) {
            $_data = $data;
        } else {
            $_data = array();

            foreach ($this->keys as $_key) {
                $_data[] = isset($data[$_key]) ? $data[$_key] : null;
            }
        }

        $this->write($_data);
        $this->rowsOut++;
    }

    public function close()
    {
        if (is_resource($this->resource)) {
            $this->writeHeader(true);
        }

        parent::close();

        return $this->rowsOut;
    }

    /**
     * @param array $data
     */
    protected function write($data = [])
    {
        if (null === $this->resource) {
            throw new FileException('The file must be open to write data.');
        }

        $_values = [];

        foreach ($data as $_value) {
            if (null === $_value) {
                if (null !== $this->nullValue) {
                    $_values[] = $this->nullValue;
                    continue;
                }

                $_values[] = !$this->wrapWhitespace ? null : ($this->wrapper . $this->wrapper);
                continue;
            }

            if ($this->lazyWrap && false === strpos($_value, $this->delimiter) && (empty($this->wrapper) || false === strpos($_value, $this->wrapper))) {
                $_values[] = $_value;
                continue;
            }

            switch ($this->escape) {
                case Escapes::DOUBLED:
                    $_value = str_replace($this->wrapper, $this->wrapper . $this->wrapper, $_value);
                    break;

                case Escapes::SLASHED:
                    $_value = str_replace($this->wrapper, '\\' . $this->wrapper, str_replace('\\', '\\\\', $_value));
                    break;
            }

            $_values[] = $this->wrapper . $_value . $this->wrapper;
        }

        $_line = implode($this->delimiter, $_values);

        if ($this->appendEOL) {
            $_line .= $this->lineBreak;
        } elseif ($this->linesOut > 0) {
            $_line = $this->lineBreak . $_line;
        }

        $_lineSize = function_exists('mb_strlen') ? mb_strlen($_line) : strlen($_line);

        if (false === ($_byteCount = $this->writeStream($this->resource, $_line))) {
            throw new FileException('Error writing to file: ' . $this->filename);
        }

        if ($_byteCount < $_lineSize) {
            throw new FileException('Failed to write entire buffer to file: ' . $this->filename);
        }

        $this->linesOut++;
    }

    /**
     * @param resource $resource Resource to write to
     * @param string   $data     Data to write
     *
     * @return int Bytes written
     */
    protected function writeStream($resource, $data)
    {
        for ($_out = 0, $_len = mb_strlen($data); $_out < $_len; $_out += $_bo) {
            if (false === ($_bo = fwrite($resource, substr($data, $_out)))) {
                break;
            }
        }

        return $_out;
    }

    /**
     * @param bool  $autoOnly
     * @param array $header
     */
    protected function writeHeader($autoOnly = false, array $header = null)
    {
        if ($autoOnly && !$this->autoWriteHeader) {
            return;
        }

        if (null === $header) {
            $header = $this->keys;
        }

        if (!is_array($header)) {
            $header = array((string)$header);
        }

        $this->write($header);
        $this->autoWriteHeader = false;
    }

    /**
     * @param boolean $appendEOL
     *
     * @return LineWriter
     */
    public function setAppendEOL($appendEOL)
    {
        $this->appendEOL = $appendEOL;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAppendEOL()
    {
        return $this->appendEOL;
    }

    /**
     * @param boolean $autoWriteHeader
     *
     * @return LineWriter
     */
    public function setAutoWriteHeader($autoWriteHeader)
    {
        $this->autoWriteHeader = $autoWriteHeader;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getAutoWriteHeader()
    {
        return $this->autoWriteHeader;
    }

    /**
     * @param boolean $lazyWrap
     *
     * @return LineWriter
     */
    public function setLazyWrap($lazyWrap)
    {
        $this->lazyWrap = $lazyWrap;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getLazyWrap()
    {
        return $this->lazyWrap;
    }

    /**
     * @param string $lineBreak
     *
     * @return LineWriter
     */
    public function setLineBreak($lineBreak)
    {
        $this->lineBreak = $lineBreak;

        return $this;
    }

    /**
     * @return string
     */
    public function getLineBreak()
    {
        return $this->lineBreak;
    }

    /**
     * @return int
     */
    public function getLinesOut()
    {
        return $this->linesOut;
    }

    /**
     * @param null|string $nullValue
     *
     * @return LineWriter
     */
    public function setNullValue($nullValue)
    {
        $this->nullValue = $nullValue;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getNullValue()
    {
        return $this->nullValue;
    }

    /**
     * @return int
     */
    public function getRowsOut()
    {
        return $this->rowsOut;
    }

    /**
     * @param boolean $wrapWhitespace
     *
     * @return LineWriter
     */
    public function setWrapWhitespace($wrapWhitespace)
    {
        $this->wrapWhitespace = $wrapWhitespace;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getWrapWhitespace()
    {
        return $this->wrapWhitespace;
    }
}
