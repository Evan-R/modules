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
 * @class ClassFooter
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class ClassFooter extends Stub
{
    public function __construct()
    {
    }

    public function dump()
    {
        return "}".PHP_EOL;
    }
}
