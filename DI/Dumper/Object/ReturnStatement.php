<?php

/**
 * This File is part of the Selene\Module\DI\Dumper\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Dumper\Object;

use \Selene\Module\Writer\Writer;
use \Selene\Module\Writer\GeneratorInterface;

/**
 * @class ReturnStatement
 * @package Selene\Module\DI\Dumper\Object
 * @version $Id$
 */
class ReturnStatement implements GeneratorInterface
{
    /**
     * writer
     *
     * @var Writer
     */
    private $writer;

    /**
     * indent
     *
     * @var int
     */
    private $indent;

    /**
     * value
     *
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param mixed $value
     * @param int $indent
     */
    public function __construct($value, $indent = 0)
    {
        $this->indent = $indent;
        $this->value = $value;
        $this->writer = new Writer;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $this->writer->writeln(sprintf('return %s;', $this->value));
        $this->writer->setOutputIndentation(max(0, ($this->indent / 4)));

        return $raw ? $this->writer : $this->writer->dump();
    }

    /**
     * __toString
     *
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->generate();
    }
}
