<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Processor;

use \Selene\Components\DI\Container;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Processor\ResolveCallerMethodCalls;

class ResolveCallerMethodCallsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\DI\Processor\ProcessInterface', new ResolveCallerMethodCalls);
    }

    /** @test */
    public function itShouldDetectInvalidCallers()
    {
        $container = new Container;

        $container->define('foo', 'stdClass')
            ->addSetter('setBar');

        $process = new ResolveCallerMethodCalls;

        try {
            $process->process($container);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals(
                'Service "foo" is configured to call "setBar" on stdClass. This method does not exist.',
                $e->getMessage()
            );
            return;
        }

        $this->fail();
    }
}
