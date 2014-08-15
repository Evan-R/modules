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

use \Selene\Module\Writer\Object\Method;
use \Selene\Module\Writer\Object\Property;
use \Selene\Module\Writer\Object\TraitWriter;

/**
 * @class ConstantTest
 * @package Selene\Module\Writer\Tests\Object
 * @version $Id$
 */
class TraitWriterTest extends ObjectWriterTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\Writer\Object\TraitWriter', $this->newObw());
    }

    /** @test */
    public function itShouldCompileToConstnatString()
    {
        $tw = $this->newObw('FooTrait', 'Acme\Traits');

        $tw->addProperty(new Property('foo'));
        $tw->addTrait('Acme\Traits\BarTrait');
        $tw->addTrait('Acme\Test\HelperTrait');
        $tw->useTraitMethodAs('Acme\Traits\BarTrait', 'getFoo', 'bla');
        $tw->replaceTraitConflict('Acme\Traits\BarTrait', 'Acme\Test\HelperTrait', 'retrieve');
    }

    protected function newObw($name = 'MyObject', $namespace = null)
    {
        return new TraitWriter($name, $namespace);
    }
}
