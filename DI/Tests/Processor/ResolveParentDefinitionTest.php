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

        $container->define('concrete', 'Acme\Concrete')->setParent('abstract_parent');

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');


        $this->assertSame('Acme\Concrete', $def->getClass());
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
            'concrete',
            $def = (new ParentDefinition('abstract_parent'))
                ->setClass('Acme\Concrete')
                ->addArgument('baz')
        );

        $def->replaceArgument('baz', 0);

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');


        $this->assertSame('Acme\Concrete', $def->getClass());
        $this->assertSame(['baz', 'bar', 'baz'], $def->getArguments());
        $this->assertFalse($def->isAbstract());
    }

    /** @test */
    public function itIsExpectedThatThisTestTestsSomething()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setAbstract(true)
            ->addArgument('foo')
            ->addArgument('bar')
            ->addSetter('setFoo', ['a', 'b']);

        $container->define('concrete', 'Acme\Concrete')
            ->addSetter('setBar', ['a', 'b'])
            ->setParent('abstract_parent');

        $container->setDefinition(
            'concrete_b',
            (new ParentDefinition('abstract_parent'))
                ->addSetter('setBar', ['a', 'b'])
                ->setParent('abstract_parent')
        );

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');

        $setters = $def->getSetters();

        $this->assertSame(['setFoo' => ['a', 'b']], $setters[0]);
        $this->assertSame(['setBar' => ['a', 'b']], $setters[1]);

        $def = $container->getDefinition('concrete_b');

        $setters = $def->getSetters();

        $this->assertSame(['setFoo' => ['a', 'b']], $setters[0]);
        $this->assertSame(['setBar' => ['a', 'b']], $setters[1]);
    }

    /** @test */
    public function itadjskldjaskjdkjsdj()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setAbstract(true)
            ->addArgument('foo')
            ->addArgument('bar')
            ->addSetter('setFoo', ['a', 'b']);

        $container->define('concrete', 'Acme\Concrete')
            ->setFactory('FooFactory', 'make')
            ->setParent('abstract_parent');

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');
    }

    /** @test */
    public function itaasds()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setAbstract(true)
            ->addArgument('foo')
            ->addArgument('bar')
            ->addSetter('setFoo', ['a', 'b']);

        $container->setDefinition(
            'concrete',
            (new ParentDefinition('abstract_parent'))
                ->setFactory('FooFactory', 'make')
        );

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');
    }

    /** @test */
    public function assertThatDefinitionInheritsFile()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setFile('somefile');

        $container->define('concrete', 'Acme\Concrete')->setParent('abstract_parent');

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');

        $this->assertTrue($def->requiresFile());
    }

    /** @test */
    public function assertThatDefinitionKeepsFile()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent');

        $container->define('concrete', 'Acme\Concrete')
            ->setFile('somefile')
            ->setParent('abstract_parent');

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');

        $this->assertTrue($def->requiresFile());
    }

    /** @test */
    public function assertThatDefinitionRemainsAbstract()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')->setAbstract(false);

        $container->define('concrete', 'Acme\Concrete')
            ->setAbstract(true)
            ->setParent('abstract_parent');

        $container->setDefinition(
            'conctete_b',
            (new ParentDefinition('abstract_parent'))
                ->setAbstract(true)
        );

        $process = new ResolveParentDefinition;

        $process->process($container);

        $def = $container->getDefinition('concrete');

        $this->assertTrue($def->isAbstract());

        $def = $container->getDefinition('conctete_b');

        $this->assertTrue($def->isAbstract());
    }

    /** @test */
    public function injectedDefinitionsShouldBeSpared()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')->setAbstract(false);

        $container->setDefinition(
            'concrete',
            $def = (new ParentDefinition('abstract_parent'))
                ->setInjected(true)
        );

        $process = new ResolveParentDefinition;

        $process->process($container);

        $this->assertSame($def, $container->getDefinition('concrete'));
    }

    /** @test */
    public function itShouldInheritMetaData()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setMetaData('tagged')
            ->setAbstract(true);

        $parent = $container->define('concrete')
            ->setParent('abstract_parent');

        $container->setDefinition(
            'concrete_b',
            $def = (new ParentDefinition('abstract_parent'))
        );

        $process = new ResolveParentDefinition;

        $process->process($container);

        $this->assertTrue($container->getDefinition('concrete')->hasMetaData('tagged'));
        $this->assertTrue($container->getDefinition('concrete_b')->hasMetaData('tagged'));
    }

    /** @test */
    public function itShouldRemoveMetaData()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setMetaData('tagged', ['a'])
            ->setAbstract(true);

        $container->setDefinition(
            'concrete',
            $def = (new ParentDefinition('abstract_parent'))
        );

        $def->removeMetaData('tagged');

        $process = new ResolveParentDefinition;

        $process->process($container);

        $this->assertFalse($container->getDefinition('concrete')->hasMetaData('tagged'));
    }

    /** @test */
    public function itShouldReplaceMetaData()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setMetaData('tagged', ['a'])
            ->setAbstract(true);

        $container->setDefinition(
            'concrete',
            $def = (new ParentDefinition('abstract_parent'))
        );

        $def->removeMetaData('tagged');
        $def->setMetaData('tagged', ['b']);

        $process = new ResolveParentDefinition;

        $process->process($container);

        $this->assertTrue($container->getDefinition('concrete')->hasMetaData('tagged'));
        $this->assertSame(['b'], $container->getDefinition('concrete')->getMetaData('tagged')->getParameters());
    }

    /** @test */
    public function itShouldOverWriteMetaData()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setMetaData('tagged', ['a'])
            ->setAbstract(true);

        $container->setDefinition(
            'concrete',
            $def = (new ParentDefinition('abstract_parent'))
        );

        $def->setMetaData('tagged', ['b']);

        $process = new ResolveParentDefinition;

        $process->process($container);

        $this->assertTrue($container->getDefinition('concrete')->hasMetaData('tagged'));
        $this->assertSame(['b'], $container->getDefinition('concrete')->getMetaData('tagged')->getParameters());
    }

    /** @test */
    public function itShouldAppendMetaData()
    {
        $container = new Container;

        $parent = $container->define('abstract_parent', 'Acme\AbstractParent')
            ->setMetaData('tagged', ['a'])
            ->setAbstract(true);

        $container->setDefinition(
            'concrete',
            $def = (new ParentDefinition('abstract_parent'))
        );

        $def->setMetaData('tagged_b', ['b']);

        $process = new ResolveParentDefinition;

        $process->process($container);

        $this->assertTrue($container->getDefinition('concrete')->hasMetaData('tagged'));
        $this->assertTrue($container->getDefinition('concrete')->hasMetaData('tagged_b'));
    }
}
