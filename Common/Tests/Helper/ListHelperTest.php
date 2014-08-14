<?php

/**
 * This File is part of the Selene\Module\Common\Tests\Helper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Helper;

use \Selene\Module\Common\Helper\ListHelper;

/**
 * @class ListHelperTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Module\Common\Tests\Helper
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ListHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function testArrayGetShouldReturnNullOnUnknowenKeys()
    {
        $this->assertNull(ListHelper::arrayGet(['foo' => ['bar' => 'baz']], 'baz.bar'));
        $this->assertNull(ListHelper::arrayGet(['foo' => ['bar' => 'baz']], 'fo.baz'));
    }

    /**
     * @test
     * @dataProvider arrayGetDataProvider
     */
    public function testArrayGet($query, $array, $expected)
    {
        $this->assertEquals($expected, ListHelper::arrayGet($array, $query));
    }

    /** @test */
    public function testArrayGetReturnInput()
    {
        $this->assertEquals(['a' => 'b'], ListHelper::arrayGet(['a' => 'b']));
        $this->assertEquals(['a' => 'b'], ListHelper::arrayGet(['a' => 'b']));
    }

    /** @test */
    public function itArrayUnset()
    {
        $data = ['foo' => ['bar' => 'baz']];

        $this->assertTrue(isset($data['foo']['bar']));

        ListHelper::arrayUnset($data, 'foo.bar');

        $this->assertTrue(isset($data['foo']));
        $this->assertFalse(isset($data['foo']['bar']));

        $data = ['foo' => ['bar' => ['baz' => 'boom']]];

        ListHelper::arrayUnset($data, 'foo.bar.bame');
        $this->assertTrue(isset($data['foo']['bar']));
        $this->assertFalse(isset($data['foo']['bar']['bame']));
    }

    /** @test */
    public function testArraySet()
    {
        $array = [];
        ListHelper::arraySet($array, 'foo', 'bar');
        ListHelper::arraySet($array, 'service.location.locale', 'en');
        ListHelper::arraySet($array, 'service.location.name', 'myservice');
        ListHelper::arraySet($array, 'service.namespace', 'myserviceNS');
        ListHelper::arraySet($array, 'service.location.0', 'in1');
        ListHelper::arraySet($array, 'service.location.1', 'in2');

        $this->assertTrue(isset($array['foo']) && $array['foo'] === 'bar');
        $this->assertTrue(isset($array['service']));
        $this->assertTrue(
            isset($array['service']['namespace']) && $array['service']['namespace'] === 'myserviceNS'
        );
        $this->assertTrue(isset($array['service']['location']));
        $this->assertTrue(
            isset($array['service']['location']['locale']) && $array['service']['location']['locale'] === 'en'
        );
        $this->assertTrue(
            isset($array['service']['location']['name']) && $array['service']['location']['name'] === 'myservice'
        );

        $data = [];

        ListHelper::arraySet($data, 'foo', 'bar');
        ListHelper::arraySet($data, 'baz', ['doo']);
        ListHelper::arraySet($data, 'baz.some', 'goo');
        ListHelper::arraySet($data, 'baz.glue', 'fuxk');
    }

    /**
     * @test
     */
    public function testArrayPluck()
    {
        $objectA = new \StdClass();
        $objectA->name = 'obj-a';
        $objectA->id   = 1;

        $objectB = new \StdClass();
        $objectB->name = 'obj-b';
        $objectB->id   = 2;

        $array = ['name' => 'array', 'id' => 3];

        $this->assertEquals(['obj-a', 'obj-b'], ListHelper::arrayPluck('name', [$objectA, $objectB]));
        $this->assertEquals(['obj-a', 'obj-b', 'array'], ListHelper::arrayPluck('name', [$objectA, $objectB, $array]));
    }

    /**
     * @test
     */
    public function testArrayColumn()
    {
        $in = [
            [
                'id' => '12',
                'name' => 'rand',
                'handle' => 'xkd23',
            ],
            [
                'id' => '14',
                'name' => 'band',
                'handle' => 'xkd25',
            ],
            [
                'id' => '22',
                'name' => 'land',
                'handle' => 'xkd77',
            ],
        ];

        $this->assertEquals(['12', '14', '22'], ListHelper::columnize($in, 'id'));
        $this->assertEquals(
            ['xkd23' => '12', 'xkd25' => '14', 'xkd77' => '22'],
            ListHelper::columnize($in, 'id', 'handle')
        );
    }

    /** @test */
    public function testArrayNumeric()
    {
        $this->assertTrue(ListHelper::arrayIsList([1, 2, 3]));
        $this->assertFalse(ListHelper::arrayIsList([1, 'foo' => 'bar', 3]));
    }

    /** @test */
    public function testArrayCompact()
    {
        $this->assertEquals([1, 2, 'a', 'b'], ListHelper::arrayCompact([1, '', 2, false, 'a', null, 'b']));
        $this->assertEquals(
            ['foo' => true, 'bar' => 1],
            ListHelper::arrayCompact(['foo' => true, 'boo' => 0, 'bar' => 1])
        );
    }

    /** @test */
    public function testArrayZip()
    {
        $this->assertEquals(
            [['a', 'A', 1], ['b', 'B', 2], ['c', 'C', 3]],
            ListHelper::arrayZip(['a', 'b', 'c'], ['A', 'B', 'C'], [1, 2, 3])
        );
    }

    /** @test */
    public function testArrayMax()
    {
        $this->assertEquals(3, ListHelper::arrayMax([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /** @test */
    public function testArrayMin()
    {
        $this->assertEquals(2, ListHelper::arrayMin([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    public function arrayGetDataProvider()
    {
        return [
            [
                'foo.bar',
                ['foo' => ['bar' => 'baz']],
                'baz'
            ],
            [
                'foo.bar.baz',
                ['foo' => ['bar'=> ['baz' => 'boom']]],
                'boom'
            ],
            [
                'foo.bar.baz.boom',
                ['foo' => ['bar'=> ['baz' => ['boom' => 'baz']]]],
                'baz'
            ],
            [
                'foo.bar.0',
                ['foo' => ['bar' => [1, 2, 3]]],
                1
            ],
            [
                'foo.bar.1',
                ['foo' => ['bar' => [1, 2, 3]]],
                2
            ]
        ];
    }
}
