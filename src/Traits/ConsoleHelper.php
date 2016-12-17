<?php namespace ChaoticWave\BlueVelvet\Traits;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * A trait that adds shortcuts for artisan commands
 *
 * @property OutputInterface|OutputStyle $output
 * @property string                      $name
 */
trait ConsoleHelper
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string The currently buffered output
     */
    private $_hccLineBuffer;
    /**
     * @type string An optional prefix, such as the command name, which will be prepended to output
     */
    private $_hccOutputPrefix;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Displays the command's name and info
     *
     * @param bool $newline If true, a blank line is added to the end of the header
     *
     * @return $this
     */
    protected function writeHeader($newline = true)
    {
        //  Don't write stuff if we're being quiet.
        if (!$this->output || OutputInterface::VERBOSITY_QUIET == $this->output->getVerbosity()) {
            return $this;
        }

        $_name = config('app.name');
        $_version = config('app.version');
        $_copyright = config('app.copyright');

        if ($_name && $_version) {
            $this->output->writeln($this->context($_name, 'info') . ' (' . $this->context($_version, 'comment') . ')');

            if (!empty($_copyright)) {
                $this->output->writeln($this->context($_copyright, 'info') . ($newline ? PHP_EOL : null));
            }
        }

        return $this;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function writeln($messages, $context = null, $type = OutputInterface::OUTPUT_NORMAL)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output && $this->output->writeln($this->formatMessages($messages, $context), $type);
    }

    /**
     * @param string|array $messages
     * @param bool         $newline
     * @param string       $context The message context (info, comment, error, or question)
     * @param int          $type
     */
    protected function write($messages, $newline = false, $context = null, $type = OutputInterface::OUTPUT_NORMAL)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->output && $this->output->write($this->formatMessages($messages, $context), $newline, $type);
    }

    /**
     * @param string $content The content to wrap
     * @param string $tag     The tag to wrap content
     *
     * @return string
     */
    protected function context($content, $tag)
    {
        return '<' . $tag . '>' . $content . '</' . $tag . '>';
    }

    /**
     * Buffers a string (optionally contextual) to write when flush() is called
     *
     * @param string      $text
     * @param string|null $context
     *
     * @return $this
     */
    protected function concat($text, $context = null)
    {
        $this->_hccLineBuffer .= ($context ? $this->context($text, $context) : $text);

        return $this;
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asInfo($text)
    {
        return $this->concat($text, 'info');
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asComment($text)
    {
        return $this->concat($text, 'comment');
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asQuestion($text)
    {
        return $this->concat($text, 'question');
    }

    /**
     * Buffers an "info" string to write at a later time
     *
     * @param string $text
     *
     * @return $this
     */
    protected function asError($text)
    {
        return $this->concat($text, 'error');
    }

    /**
     * Writes any buffered text and clears the buffer
     *
     * @param string|null $message Any text to add to the buffer before flushing
     * @param string|null $context The context of $message
     */
    protected function flush($message = null, $context = null)
    {
        if (null !== $message) {
            $this->concat($message, $context);
        }

        if (!empty($this->_hccLineBuffer)) {
            $this->writeln($this->_hccLineBuffer);
        }

        $this->_hccLineBuffer = null;
    }

    /**
     * @param string|array $messages
     * @param string       $context The message context (info, comment, error, or question)
     * @param bool         $prefix  If false, text will not be prefixed
     *
     * @return array|string
     */
    protected function formatMessages($messages, $context = null, $prefix = true)
    {
        $_scrubbed = [];
        $_data = !is_array($messages) ? [$messages] : $messages;

        if (!empty($this->_hccOutputPrefix) && ': ' != substr($this->_hccOutputPrefix, -2)) {
            $this->_hccOutputPrefix = trim($this->_hccOutputPrefix, ':') . ': ';
        }

        foreach ($_data as $_message) {
            $context && ($_message = $this->context(trim($_message), $context));
            $_scrubbed[] = ($prefix && $this->_hccOutputPrefix ? $this->_hccOutputPrefix : null) . $_message;
        }

        return is_array($messages) ? $_scrubbed : $_scrubbed[0];
    }

    /**
     * @return string
     */
    public function getOutputPrefix()
    {
        return $this->_hccOutputPrefix;
    }

    /**
     * @param string $outputPrefix
     *
     * @return $this
     */
    public function setOutputPrefix($outputPrefix)
    {
        $this->_hccOutputPrefix = $outputPrefix;

        return $this;
    }
}
