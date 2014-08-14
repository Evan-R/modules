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
use \Selene\Module\DI\Reference;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\Processor\RemoveAbstractDefinition;
use \Selene\Module\DI\Processor\ResolveDefinitionArguments;

class ResolveDefinitionArgumentsTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\DI\Processor\ProcessInterface', new ResolveDefinitionArguments);
    }

    /** @test */
    public function itShouldResolveReferenceStrings()
    {
        $container = new Container;

        $container->define('foo', 'FooClass', ['$bar', 'somestring', $test = new \stdClass]);
        $container->define('bar', 'BarClass');
        $container->define('baz', 'BazClass', [['$bar']]);

        $container->getDefinition('foo')->addSetter('setBaz', ['$baz']);

        $process = new ResolveDefinitionArguments;

        $process->process($container);


        $fooArgs = $container->getDefinition('foo')->getArguments();
        $bazArgs = $container->getDefinition('baz')->getArguments();

        $setters = $container->getDefinition('foo')->getSetters();
        $setterArgs = $setters[0][key($setters[0])];

        $this->assertInstanceof('\Selene\Module\DI\Reference', $fooArgs[0]);

        $this->assertInstanceof('\Selene\Module\DI\Reference', $bazArgs[0][0]);
        $this->assertInstanceof('\Selene\Module\DI\Reference', $setterArgs[0]);

        $this->assertSame('somestring', $fooArgs[1]);
        $this->assertSame($test, $fooArgs[2]);
    }

    /** @test */
    public function itShouldAddServiceClassToFactoryArguments()
    {
        $container = new Container;

        $def = $container->define('foo', 'FooClass', [1, 2, 3])
            ->setFactory('FooFactory', 'make');

        $process = new ResolveDefinitionArguments;

        $process->process($container);

        $this->assertSame(['\FooClass', 1, 2, 3], $def->getArguments());
    }
    /** @test */
    public function itIsExpectedThat()
    {
        $container = new Container;

        $container->define('bar', 'BarClass');
        $container->define('baz', 'BazClass');
        $def = $container->define('foo', 'FooClass');

        $def->setArguments([[new Reference('bar'), new Reference('baz')]]);

        $process = new ResolveDefinitionArguments;
        $process->process($container);
    }
}
