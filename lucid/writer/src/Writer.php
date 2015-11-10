<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer;

/**
 * This is the base writer that concats lines of strings to a visual block of
 * text.
 *
 * @class Writer
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Writer
{
    use Stringable;

    /**
     * ingnoreNull
     *
     * @var boolean
     */
    protected $ingnoreNull;

    /**
     * indent
     *
     * @var int
     */
    protected $indent;

    /**
     * indentLevel
     *
     * @var int
     */
    protected $indentLevel;

    /**
     * base Indentation
     *
     * @var int
     */
    protected $outputIndentation;

    /**
     * Line buffer
     *
     * @var array
     */
    protected $lnbuff;

    /**
     * useTabs
     *
     * @var boolean
     */
    protected $useTabs;

    /**
     * noTrailingSapce
     *
     * @var boolean
     */
    protected $noTrailingSapce;

    /**
     * Constructor.
     *
     * @param int $indentLevel
     * @param boolean $ignoreNull
     */
    public function __construct($indentLevel = 4, $ignoreNull = false)
    {
        $this->lnbuff = [];
        $this->indent = 0;
        $this->indentLevel = $indentLevel;
        $this->outputIndentation = 0;
        $this->ignoreNull($ignoreNull);

        $this->useTabs = false;
        $this->noTrailingSpace = true;
    }

    /**
     * Use tabs for indentation.
     *
     * @api
     * @return void
     */
    public function useTabs()
    {
        $this->useTabs = true;
    }

    /**
     * allowTrailingSpace
     *
     * @api
     * @return void
     */
    public function allowTrailingSpace($space)
    {
        $this->noTrailingSpace = !(bool)$space;
    }

    /**
     * Ignores adding null values to Writer::writeln() is null.
     *
     * @param boolean $ignore
     *
     * @api
     * @return void
     */
    public function ignoreNull($ignore = false)
    {
        $this->ignoreNull = (bool)$ignore;
    }

    /**
     * Set the level of the output indentation.
     *
     * The default level is 0, 1 means one indent, etc.
     *
     * @param int $level
     *
     * @api
     * @return void
     */
    public function setOutputIndentation($level = 0)
    {
        $this->outputIndentation = ($this->indentLevel * $level);
    }

    /**
     * Get the level of the output indentation.
     *
     * @return int
     */
    public function getOutputIndentation()
    {
        return $this->outputIndentation;
    }

    /**
     * Adds a line to the line stack.
     *
     * @param string $str
     *
     * @api
     * @return Writer
     */
    public function writeln($str = null)
    {
        if (null === $str && $this->ignoreNull) {
            return $this;
        }

        $this->addStr($str);

        return $this;
    }

    /**
     * Appends a string to the last line.
     *
     * @param string $str
     *
     * @api
     * @return Writer
     */
    public function appendln($str)
    {
        $line = array_pop($this->lnbuff);

        $line = $line.$str;

        $this->lnbuff[] = $line;

        return $this;
    }

    /**
     * Removes the last line.
     *
     * @api
     * @return writer
     */
    public function popln()
    {
        array_pop($this->lnbuff);

        return $this;
    }

    /**
     * Replace a line at a given index.
     *
     * @param string $line
     * @param int $index
     *
     * @api
     * @return Writer
     */
    public function replaceln($str, $index = 0)
    {
        if ($index < 0 || ($index + 1) > count($this->lnbuff)) {
            throw new \OutOfBoundsException(sprintf('replaceln: undefined index "%s".', $index));
        }

        $this->addStr($str, $index);

        return $this;
    }

    /**
     * Remove a line by a given index.
     *
     * @param int $index
     *
     * @api
     * @return Writer
     */
    public function removeln($index = 0)
    {
        if ($index < 0 || ($index + 1) > count($this->lnbuff)) {
            throw new \OutOfBoundsException(sprintf('removeln: undefined index "%s".', $index));
        }

        array_splice($this->lnbuff, $index, 1);

        return $this;
    }

    /**
     * Adds an indentation to the following line.
     *
     * @api
     * @return Writer
     */
    public function indent()
    {
        $this->indent += $this->indentLevel;

        return $this;
    }

    /**
     * Removes the previous indentation.
     *
     * @api
     * @return Writer
     */
    public function outdent()
    {
        $this->indent -= $this->indentLevel;
        $this->indent = max(0, $this->indent);

        return $this;
    }

    /**
     * Inserts a blanc line to the line stack.
     *
     * @api
     * @return Writer
     */
    public function newline()
    {
        $this->lnbuff[] = '';

        return $this;
    }

    /**
     * Concatenates the line stack into a single string.
     *
     * @api
     * @return string
     */
    public function dump()
    {
        $pad = $this->padString('', $this->outputIndentation);

        return preg_replace('/^\s+$/m', '', $pad.implode("\n".$pad, $this->lnbuff));
    }

    /**
     * addStr
     *
     * @param mixed $str
     * @param int $index
     *
     * @return void
     */
    protected function addStr($str, $index = null)
    {
        try {
            $str = (string)$str;
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Input value must be stringable.');
        }

        foreach (explode("\n", (string)$str) as $i => $line) {

            if (0 !== strlen($line)) {
                $this->pushStr($line, $index ? $index + $i : null);
                continue;
            }

            if (null === $index) {
                $this->newline();
            } else {
                $this->lnbuff[$index + $i] = null;
            }
        }
    }

    /**
     * pushStr
     *
     * @param mixed $str
     *
     * @return void
     */
    protected function pushStr($str, $index = null)
    {
        $line = $this->padString($str, $this->indent);

        if ($this->noTrailingSpace) {
            $line = rtrim($line);
        }

        if (null !== $index) {
            $this->lnbuff[(int)$index] = $line;

            return;
        }

        $this->lnbuff[] = $line;
    }

    /**
     * indentLine
     *
     * @param mixed $str
     *
     * @return string
     */
    protected function padString($str, $indent = 0)
    {
        if ($indent === 0 || null === $str) {
            return $str;
        }

        return sprintf('%s%s', $this->getIndent($indent), $str);
    }

    /**
     * getIndent
     *
     * @param int $indent
     *
     * @return string space or tab chars
     */
    protected function getIndent($indent)
    {
        if ($this->useTabs) {
            $level = $indent / $this->indentLevel;

            return str_repeat(chr(11), $level);
        }

        return str_repeat(' ', $indent);
    }
}
