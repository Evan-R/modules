<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Dumper\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Dumper\Object;

use \Selene\Components\DI\Container;
use \Selene\Components\DI\Dumper\Object\ServiceMethod;

/**
 * @class ServiceMethodTest
 * @package Selene\Components\DI\Tests\Dumper\Object
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

        $this->assertInstanceof('\Selene\Components\DI\Dumper\Object\ServiceMethodBody', $sm->getBody());
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
