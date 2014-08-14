<?php

/**
 * This File is part of the Selene\Module\Writer\Tests\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\Object;

use \Selene\Module\Writer\Object\Constant;
use \Selene\Module\Writer\Object\InterfaceWriter;
use \Selene\Module\Writer\Object\InterfaceMethod;

/**
 * @class ClassWriterTest
 * @package Selene\Module\Writer\Tests\Object
 * @version $Id$
 */
class InterfaceWriterTest extends ObjectWriterTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\Writer\Object\ObjectWriter', new InterfaceWriter('MyObject'));
    }

    public function itShouldBeExtendable()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $cwr->noAutoGenerateTag();
        $cwr->setParent('\Acme\BarInterface');

        $this->assertEquals(file_get_contents(__DIR__.'/Fixures/interface.0.php'), $cwr->generate());
    }

    /** @test */
    public function itShouldHaveConstants()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $cwr->noAutoGenerateTag();

        $cwr->setConstants([
            new Constant('t_foo', '12'),
            new Constant('t_bar', '13')
        ]);

        $this->assertEquals(file_get_contents(__DIR__.'/Fixures/interface.4.php'), $cwr->generate());
    }

    /** @test */
    public function itShouldHaveConstantsAndMethods()
    {
        $cwr = $this->newObw('Acme\FooInterface');
        $cwr->noAutoGenerateTag();

        $cwr->setConstants([
            new Constant('t_foo', '12')
        ]);

        $cwr->addMethod(new InterfaceMethod('setFoo'));
        $cwr->addMethod(new InterfaceMethod('setBar'));

        $this->assertEquals(file_get_contents(__DIR__.'/Fixures/interface.4.1.php'), $cwr->generate());
    }

    protected function newObw($name = 'MyObject', $namespace = null)
    {
        return new InterfaceWriter($name, $namespace);
    }
}
