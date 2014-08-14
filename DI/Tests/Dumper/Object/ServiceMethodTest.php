<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Dumper\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Dumper\Object;

use \Selene\Module\DI\Container;
use \Selene\Module\DI\Dumper\Object\ServiceMethod;

/**
 * @class ServiceMethodTest
 * @package Selene\Module\DI\Tests\Dumper\Object
 * @version $Id$
 */
class ServiceMethodTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldReturnServiceMethodBodyIfNotSet()
    {
        $container = new Container;

        $container->define('foo', 'stdClass');

        $sm = new ServiceMethod($container, 'foo');

        $this->assertInstanceof('\Selene\Module\DI\Dumper\Object\ServiceMethodBody', $sm->getBody());
    }

    /** @test */
    public function itShouldAutoSetItsName()
    {
        $container = new Container;

        $container->define('foo', 'stdClass')->setInternal(true);

        $sm = new ServiceMethod($container, 'foo');
        $sm->setBody('return null;');

        $this->assertSame(file_get_contents(__DIR__.'/../Fixures/servicemethod.0.1'), $sm->generate().PHP_EOL);
    }

    /** @test */
    public function itShouldLookEquallyWhenDumped()
    {
        $container = new Container;

        $container->define('foo', 'stdClass');

        $sm = new ServiceMethod($container, 'foo');

        $sm->setBody('return null;');

        $this->assertSame(file_get_contents(__DIR__.'/../Fixures/servicemethod.0'), $sm->generate().PHP_EOL);
    }
}
