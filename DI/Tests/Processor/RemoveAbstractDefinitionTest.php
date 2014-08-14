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

use \Mockery as m;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\Processor\RemoveAbstractDefinition;

class RemoveAbstractDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessInterface', new RemoveAbstractDefinition);
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
