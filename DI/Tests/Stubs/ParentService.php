<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Stubs;

/**
 * @class ParentSevice
 * @package Selene\Module\DI\Tests\Stubs
 * @version $Id$
 */
class ParentService
{
    protected $foo;
    protected $bar;

    public function __construct($foo = null)
    {
        $this->foo = $foo;
    }

    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    public function getFoo()
    {
        return $this->foo;
    }
    public function getBar()
    {
        return $this->bar;
    }
}
