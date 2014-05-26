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

use \Mockery as m;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Processor\RemoveAbstractDefinition;

class RemoveAbstractDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\DI\Processor\ProcessInterface', new RemoveAbstractDefinition);
    }

    /** @test */
    public function itShouldRemoveAbstractDefinitions()
    {
        $container = new Container;

        $container->define('foo')->setAbstract(true);
        $container->define('bar')->setAbstract(false);
        $container->define('baz')->setAbstract(false);
        $container->define('bam')->setAbstract(true);

        $process = new RemoveAbstractDefinition;

        $process->process($container);

        $this->assertFalse($container->hasDefinition('foo'));
        $this->assertTrue($container->hasDefinition('bar'));
        $this->assertTrue($container->hasDefinition('baz'));
        $this->assertFalse($container->hasDefinition('bam'));
    }
}
