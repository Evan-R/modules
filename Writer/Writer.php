<?php

/*
 * This File is part of the Selene\Module\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer;

/**
 * @class Writer
 * @package Selene\Module\Writer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Writer
{
    use Stringable;

    const INDENT_TAB = "\v";
    const INDENT_SPACE = ' ';

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
     * outputIndentation
     *
     * @var int
     */
    protected $outputIndentation;

    /**
     * lines
     *
     * @var array
     */
    protected $lines;

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
        $this->lines = [];
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
        $this->tab = chr(11);
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
        $line = array_pop($this->lines);

        $line = $line.$str;

        $this->lines[] = $line;

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
        array_pop($this->lines);

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
        if ($index < 0 || ($index + 1) > count($this->lines)) {
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
        if ($index < 0 || ($index + 1) > count($this->lines)) {
            throw new \OutOfBoundsException(sprintf('removeln: undefined index "%s".', $index));
        }

        array_splice($this->lines, $index, 1);

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
        $this->lines[] = '';

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

        return preg_replace('/^\s+$/m', '', $pad.implode("\n".$pad, $this->lines));
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
                $this->lines[$index + $i] = null;
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
            $this->lines[(int)$index] = $line;

            return;
        }

        $this->lines[] = $line;
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

        if ($this->useTabs) {
            $level = $indent / $this->indentLevel;

            return sprintf('%s%s', str_repeat(self::INDENT_TAB, $level), $str);
        }

        return sprintf('%s%s', str_repeat(self::INDENT_SPACE, $indent), $str);
    }
}
