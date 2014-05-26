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
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\ParentDefinition;
use \Selene\Components\DI\Definition\DefinitionInterface;
use \Selene\Components\DI\Processor\ResolveParentDefinition;

class ResolveParentDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\DI\Processor\ProcessInterface', new ResolveParentDefinition);
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->addArgument('foo')
            ->addArgument('bar');

        $container->define('conctete', 'Acme\Concrete')->setParent('abstract_parent');

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('conctete');

        $this->assertSame(['foo', 'bar'], $def->getArguments());
    }

    /** @test */
    public function itIsExpectedThatIs()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setAbstract(true)
            ->addArgument('foo')
            ->addArgument('bar');

        $container->setDefinition(
            'conctete',
            $def = (new ParentDefinition('abstract_parent'))->setClass('Acme\Concrete')
        );

        $def->replaceArgument('baz', 0);

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('conctete');

        $this->assertSame(['baz', 'bar'], $def->getArguments());
        $this->assertFalse($def->isAbstract());
    }
}
