<?php

/**
 * This File is part of the Selene\Module\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests;

use \Selene\Module\DI\Tests\Stubs\ContainerAwareStub;
use \Selene\Module\DI\Container;

class ContainerAwareTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeContainerAware()
    {
        $container = new Container;

        $aware = new ContainerAwareStub;

        $this->assertInstanceof('\Selene\Module\DI\ContainerAwareInterface', $aware);

        $aware->setContainer($container);

        $this->assertSame($container, $aware->getContainer());
    }
}
