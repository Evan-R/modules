<?php

/**
 * This File is part of the Selene\Components\DI\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Stubs;

use \Selene\Components\DI\Dumper\Traits\FormatterTrait;

/**
 * @class Lines implements StubInterface
 * @see StubInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Lines implements StubInterface
{
    use FormatterTrait {
        FormatterTrait::indent as protected doIndent;
    }

    /**
     * lines
     *
     * @var array
     */
    protected $lines;

    /**
     * indents
     *
     * @var \SplStack
     */
    protected $indents;

    /**
     * outputIndentation
     *
     * @var int
     */
    protected $outputIndentation;

    /**
     *
     * @access public
     * @return mixed
     */
    public function __construct()
    {
        $this->lines = [];

        $this->indents = new \SplStack;
        $this->indents->push(0);

        $this->setOutputIndentation(0);
    }

    /**
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->dump();
    }

    /**
     * dump
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        return $this->doIndent($this->outputIndentation) . implode($this->getImplodeSeparator(), $this->lines);
    }

    /**
     * add
     *
     * @param mixed $string
     *
     * @access public
     * @return Lines
     */
    public function add($string)
    {
        $this->lines[] = sprintf('%s%s', $this->doIndent($this->getCurrentIndent()), $string);
        return $this;
    }

    /**
     * indent
     *
     * @access public
     * @return Lines
     */
    public function indent()
    {
        $this->indents->push($this->getCurrentIndent() + 4);
        return $this;
    }

    /**
     * end
     *
     *
     * @access public
     * @return Lines
     */
    public function end()
    {
        if (0 !== $this->indents->count()) {
            $this->indents->pop();
        }
        return $this;
    }

    /**
     * emptyLine
     *
     * @access public
     * @return Line
     */
    public function emptyLine()
    {
        $this->lines[] = '';
        return $this;
    }

    /**
     * setOutputIndentation
     *
     * @param int $indent
     *
     * @access public
     * @return Lines
     */
    public function setOutputIndentation($indent = 0)
    {
        $this->outputIndentation = $indent;
        return $this;
    }

    /**
     * getCurrentIndent
     *
     * @access protected
     * @return int
     */
    protected function getCurrentIndent()
    {
        return $this->indents->count() ? $this->indents->top() : 0;
    }
    /**
     * getImplodeSeparator
     *
     * @access protected
     * @return string
     */
    protected function getImplodeSeparator()
    {
        return sprintf('%s%s', PHP_EOL, $this->doIndent($this->outputIndentation));
    }
}
