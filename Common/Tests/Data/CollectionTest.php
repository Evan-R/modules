<?php

/**
 * This File is part of the Selene\Components\Session\Tests\Collection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests\Data;

use \Selene\Components\Common\Data\Collection;

/**
 * @class CollectionTest
 * @package Selene\Components\Session\Tests\Collection
 * @version $Id$
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $collection = $this->getCollection();
        $this->assertInstanceof('Selene\Components\Common\Data\Collection', $collection);
        $this->assertInstanceof('Selene\Components\Common\Data\CollectionInterface', $collection);
    }

    /** @test */
    public function itShouldSetAndGetAttributes()
    {
        $collection = $this->getCollection();

        $collection->set('foo', ['bar' => 'baz']);

        $this->assertTrue($collection->has('foo'));

        unset($collection['foo']);

        $this->assertNull($collection->get('foo'), '->delete() should unset value');

    }

    /** @test */
    public function itShouldMergeACollection()
    {
        $collectionA = $this->getCollection();
        $collectionB = $this->getCollection();

        $collectionA->set('foo', ['bar' => 'baz', 'bam' => 'boom']);
        $collectionA->set('fuzz', 'faz');

        $collectionA->merge($collectionB);

        $this->assertTrue($collectionA->has('foo'));
        $this->assertTrue($collectionA->has('fuzz'));
    }

    /** @test */
    public function itShouldGetAllPrimaryAttributeKeys()
    {
        $collection = $this->getCollection();
        $collection->set('foo', 'bar');
        $collection->set('bar', 'bam');

        $this->assertEquals(['foo', 'bar'], $collection->keys());
    }

    /** @test */
    public function itShouldGetAllAttributes()
    {
        $collection = $this->getCollection();
        $collection->set('foo', 'bar');
        $collection->set('bar', 'bam');

        $this->assertEquals(['foo' => 'bar', 'bar' => 'bam'], $collection->all());
    }

    protected function getCollection(array $data = [])
    {
        return new Collection($data);
    }
}
