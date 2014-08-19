<?php

/*
 * This File is part of the Selene\Module\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Object;

use \Selene\Module\Writer\Writer;
use \Selene\Module\Writer\Stringable;
use \Selene\Module\Writer\GeneratorInterface;

/**
 * @class Constant
 * @package Selene\Module\Writer\Object
 * @version $Id$
 */
class Constant implements GeneratorInterface
{
    use Stringable;

    /**
     * name
     *
     * @var string
     */
    private $name;

    /**
     * value
     *
     * @var string
     */
    private $value;

    /**
     * Constructor.
     *
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name  = $name;
        $this->value = $value;
    }

    /**
     * generate
     *
     * @param boolean $raw
     *
     * @return void
     */
    public function generate($raw = self::RV_STRING)
    {
        $writer = new Writer;
        $writer->setOutputIndentation(1);
        $writer->writeln(sprintf('const %s = %s;', strtoupper($this->name), $this->value));

        return $raw ? $writer : $writer->dump();
    }
}
