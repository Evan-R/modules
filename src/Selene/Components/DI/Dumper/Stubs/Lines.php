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
 * @class Lines
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class Lines
{
    use FormatterTrait;

    /**
     * lines
     *
     * @var array
     */
    protected $lines;

    protected $indent;
    /**
     * currentIndent
     *
     * @var mixed
     */
    protected $currentIndent;

    /**
     * outPutIndentation
     *
     * @var int
     */
    protected $outPutIndentation;

    /**
     *
     * @access public
     * @return mixed
     */
    public function __construct()
    {
        $this->lines = [];
        $this->indent = 0;
        $this->currentIndent = 0;
        $this->outPutIndentation = 0;

        $this->indents = new \SplStack;
        $this->indents->push(0);
        $this->indents->push(0);
    }

    /**
     * add
     *
     * @param mixed $string
     * @param int $indent
     *
     * @access public
     * @return Lines
     */
    public function add($string, $indent = 0)
    {
        if ($indent > 0) {
            $this->indents->push((int)$indent);
        }

        $this->lines[] = $l = sprintf('%s%s', $this->indent($i = $this->getCurrentIndent()), $string);
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
        $first =  $this->indents->pop();
        $indent = $first + $this->indents->top();
        $this->indents->push($first);

        return $indent;
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
        $this->outPutIndentation = $indent;
        return $this;
    }

    /**
     * dump
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        return implode($this->getImplodeSeparator(), $this->lines);
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
     * getImplodeSeparator
     *
     * @access protected
     * @return string
     */
    protected function getImplodeSeparator()
    {
        return sprintf('%s%s', PHP_EOL, $this->indent($this->outPutIndentation));
    }
}
