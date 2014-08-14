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
use \Selene\Module\DI\Reference;
use \Selene\Module\DI\Processor\ResolveAliasWithDefinition;

class ResolveAliasWithDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessInterface', new ResolveAliasWithDefinition);
    }

    /** @test */
    public function itShouldReplaceArgumentsWithConcreteReference()
    {
        $container = new Container;

        $container->define('foo', 'stdClass');
        $bar = $container->define('bar', 'stdClass');

        $bar->addArgument(new Reference('foo_alias'));

        $container->setAlias('foo_alias', 'foo');

        $process = new ResolveAliasWithDefinition;

        $process->process($container);

        $this->assertSame('foo', (string)current($bar->getArguments()));
    }

    /** @test */
    public function itShouldReplaceSetterArgumentsWithConcreteReference()
    {

        $container = new Container;

        $container->define('foo', 'stdClass');
        $bar = $container->define('bar', 'stdClass');

        $bar->addSetter('setFoo', [new Reference('foo_alias')]);

        $container->setAlias('foo_alias', 'foo');

        $process = new ResolveAliasWithDefinition;

        $process->process($container);

        $setters = $bar->getSetters('setFoo');

        $this->assertSame('foo', (string)$setters[0]['setFoo'][0]);
    }

    /** @test */
    public function itShouldReplaceSetterArgumentsWithConcreteReferenceEvenWehnNested()
    {

        $container = new Container;

        $container->define('foo', 'stdClass');
        $bar = $container->define('bar', 'stdClass');

        $bar->addSetter('setFoo', [1, 2, [new Reference('foo_alias')]]);

        $container->setAlias('foo_alias', 'foo');

        $process = new ResolveAliasWithDefinition;

        $process->process($container);

        $setters = $bar->getSetters('setFoo');

        $args = $setters[0]['setFoo'];
        $this->assertSame('foo', (string)$args[2][0]);
    }
}
