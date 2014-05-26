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

use \Selene\Components\DI\Container;

/**
 * @class ContainerStub
 * @package Selene\Components\DI\Tests\Stubs
 * @version $Id$
 */
class ContainerStub extends Container
{
    protected function getServiceFoo()
    {
        return $this->services['foo'] = new \StdClass;
    }
}
