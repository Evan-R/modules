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

use \Selene\Module\DI\Container;

/**
 * @class ContainerStub
 * @package Selene\Module\DI\Tests\Stubs
 * @version $Id$
 */
class ContainerStub extends Container
{
    protected function getServiceFoo()
    {
        return $this->services['foo'] = new \StdClass;
    }
}
