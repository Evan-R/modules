<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Selene\Components\DI\Tests\Stubs\ContainerAwareStub;
use \Selene\Components\DI\Container;

class ContainerAwareTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeContainerAware()
    {
        $container = new Container;

        $aware = new ContainerAwareStub;

        $this->assertInstanceof('\Selene\Components\DI\ContainerAwareInterface', $aware);

        $aware->setContainer($container);

        $this->assertSame($container, $aware->getContainer());
    }
}
