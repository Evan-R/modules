<?php

/**
 * This File is part of the Selene\Components\DependencyInjection\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection\Tests\Stubs;

/**
 * @class SetterAwareService
 * @package Selene\Components\DependencyInjection\Tests\Stubs
 * @version $Id$
 */
class SetterAwareService
{
    public function setFoo(FooService $foo)
    {
        $this->foo = $foo;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
