<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Stubs;

/**
 * @class SetterAwareService
 * @package Selene\Components\DI\Tests\Stubs
 * @version $Id$
 */
class SetterAwareService
{
    public $foo;

    public $bar;

    public $name;

    public function setFoo(FooService $foo)
    {
        $this->foo = $foo;
    }

    public function setFooBar(FooService $foo, \StdClass $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }


    public function setName($name)
    {
        $this->name = $name;
    }
}
