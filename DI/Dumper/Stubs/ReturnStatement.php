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
 * @class ReturnStatement
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class ReturnStatement extends Stub
{
    /**
     * @param string $value
     * @param int $indent
     *
     * @access public
     */
    public function __construct($value, $indent = 0)
    {
        $this->indent = $indent;
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        return sprintf('%sreturn %s;', $this->indent($this->indent), $this->value);
    }
}
