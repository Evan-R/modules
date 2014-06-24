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

/**
 * @class String
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class String implements StubInterface
{
    /**
     * string
     *
     * @var string
     */
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function dump()
    {
        return $this->string;
    }

    public function __toString()
    {
        return $this->dump();
    }
}
