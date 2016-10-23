<?php namespace ChaoticWave\BlueVelvet\Data;

use ChaoticWave\BlueVelvet\Contracts\ReadsData;
use ChaoticWave\BlueVelvet\Exceptions\FileException;

/**
 * Reads files a line at a time
 */
class LineReader implements ReadsData
{
    //*************************************************************************
    //	Constants
    //*************************************************************************

    /**
     * @var integer The number of lines to skip when reading the file for the first time
     */
    protected $skip = 0;
    /**
     * @var bool
     */
    protected $whitespace = true;
    /**
     * @var bool End of File
     */
    protected $eof = false;
    /**
     * @var bool Beginning of File
     */
    protected $bof = false;
    /**
     * @var string
     */
    protected $filename;
    /**
     * @var resource
     */
    protected $resource;
    /**
     * @var array
     */
    protected $cursor;
    /**
     * @var int
     */
    protected $lineNumber = -1;
    /**
     * @var callable[]
     */
    protected $callbacks;

    //*************************************************************************
    //	Methods
    //*************************************************************************

    /**
     * @param array|mixed $filename
     */
    public function __construct($filename)
    {
        if (empty($filename) || !file_exists($filename)) {
            throw new \InvalidArgumentException('Missing or invalid "$fileName" specified.');
        }

        $this->filename = $filename;
    }

    /**
     * Choose your destructor!
     */
    public function __destruct()
    {
        is_resource($this->resource) and @fclose($this->resource);
    }

    /**
     * @param bool $rewind Rewind the resource before reading
     *
     * @return array|bool
     */
    protected function readLine($rewind = false)
    {
        (!$this->bof && !$rewind) and $this->rewind();

        if ($this->eof) {
            return false;
        }

        $_buffer = null;

        while (false !== ($_line = fgets($this->resource))) {
            //  Send raw line to callbacks
            if (!empty($this->callbacks['before'])) {
                foreach ($this->callbacks['before'] as $_callback) {
                    $_line = call_user_func($_callback, $_line, $this);

                    if (substr($_line, 0, -1) != PHP_EOL) {
                        $_line .= PHP_EOL;
                    }
                }
            }

            $_line = trim($_line);

            if ($this->whitespace && empty($_line) && empty($_buffer)) {
                continue;
            }

            $_buffer .= $_line;
            $_result = $this->parseLine($_buffer);

            //  Send cooked line to callbacks
            if (!empty($this->callbacks['after'])) {
                foreach ($this->callbacks['after'] as $_callback) {
                    if (false === ($_result = call_user_func($_callback, $_result, $this))) {
                        //	Skip this line if callback calls foul
                        continue;
                    }
                }
            }

            if (false !== $_result) {
                return $_result;
            }
        }

        if (false !== ($this->eof = feof($this->resource))) {
            if (!empty($_buffer)) {
                throw new FileException('Unparsed data in buffer from line #' . $this->lineNumber . '.');
            }

            return false;
        }

        throw new FileException('Unable to read file: ' . $this->filename);
    }

    /**
     * Opportunity to parse the line out if you want
     *
     * @param string $line
     *
     * @return mixed
     */
    protected function parseLine($line)
    {
        //	Does nothing, like the goggles.
        return $line;
    }

    /**
     * Close any open resources
     */
    public function close()
    {
        if (is_resource($this->resource) && !fclose($this->resource)) {
            throw new FileException('Error whilst closing file: ' . $this->filename);
        }

        $this->eof = true;
        $this->resource = $this->cursor = null;
        $this->lineNumber = -1;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Return the key of the current element
     *
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        if (null !== $this->current()) {
            return $this->lineNumber;
        }

        return null;
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

        return $this->cursor = $_line;
    }

    /**
     * The next line
     */
    public function next()
    {
        if (null !== $this->current()) {
            $this->cursor = null;
            $this->lineNumber++;
        }
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return (null !== $this->current());
    }

    public function rewind()
    {
        $this->close();

        if (false === ($this->resource = fopen($this->filename, 'r'))) {
            throw new FileException('Unable to open file: ' . $this->filename);
        }

        $this->eof = false;

        //	Skip the first "x" lines based on $skip property
        $_count = $this->skip;

        while ($_count && false !== ($_line = fgets($this->resource))) {
            --$_count;
        }

        $this->cursor = null;
        $this->lineNumber = 1;
        $this->bof = true;
    }

    /**
     * @param int $index
     *
     * @throws \OutOfBoundsException
     */
    public function seek($index)
    {
        $this->rewind();

        if ($index < 1) {
            throw new \OutOfBoundsException('The requested $index "' . $index . '" is not valid');
        }

        while ($this->lineNumber < $index && null !== $this->current()) {
            $this->next();
        }

        if (null === $this->current()) {
            throw new \OutOfBoundsException('The requested $index "' . $index . '" is not valid');
        }
    }

    /**
     * @param callable $callback
     * @param bool     $before If true, registers a "before read" callback. Otherwise an "after read" callback is registered
     *
     * @return \ChaoticWave\BlueVelvet\Data\LineReader
     */
    public function pushCallback($callback, $before = true)
    {
        $this->callbacks[$before ? 'before' : 'after'][] = $callback;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isBof()
    {
        return $this->bof;
    }

    /**
     * @return array
     */
    public function getCursor()
    {
        return $this->cursor;
    }

    /**
     * @return boolean
     */
    public function getEof()
    {
        return $this->eof;
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * @return resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @param boolean $whitespace
     *
     * @return $this
     */
    public function setWhitespace($whitespace = true)
    {
        $this->whitespace = $whitespace;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isWhitespace()
    {
        return $this->whitespace;
    }

    /**
     * @return int
     */
    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    /**
     * @param int $skip
     *
     * @return $this
     */
    public function setSkip($skip)
    {
        $this->skip = $skip;

        return $this;
    }

    /**
     * @return int
     */
    public function getSkip()
    {
        return $this->skip;
    }
}
