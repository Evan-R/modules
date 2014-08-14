<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Processor package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Processor;

use \Selene\Module\DI\Container;
use \Selene\Module\DI\Processor\RemoveInjectedArguments;

class RemoveInjectedArgumentsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessInterface', new RemoveInjectedArguments);
    }

    /** @test */
    public function itShouldRemoveArgsAndSettersFromInjectedServices()
    {
        $container = new Container;

        $container->define('foo')
            ->addArgument('foo')
            ->addSetter('setStuff', ['stuff'])
            ->setInjected(true);

        $container->define('bar')
            ->addArgument('bar')
            ->addSetter('setStuff', ['stuff']);

        $process = new RemoveInjectedArguments;

        $process->process($container);

        $this->assertFalse($container->getDefinition('foo')->hasArguments());
        $this->assertFalse($container->getDefinition('foo')->hasSetters());

        $this->assertTrue($container->getDefinition('bar')->hasArguments());
        $this->assertTrue($container->getDefinition('bar')->hasSetters());
    }
}
