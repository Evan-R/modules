<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests;

use Mockery as m;

/**
 * @class ServiceDriverTest
 */
class HelperFunctionsTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function testArrayGet()
    {
        $this->assertNull(array_get('baz.bar', ['foo' => ['bar' => 'baz']]));
        $this->assertNull(array_get('fo.baz', ['foo' => ['bar' => 'baz']]));

        $this->assertEquals('baz', array_get('foo.bar', ['foo' => ['bar' => 'baz']]));
        $this->assertEquals('baz', array_get('foo.bar.boo.bar', ['foo' => ['bar' => ['boo' => ['bar' => 'baz']]]]));
    }

    /**
     * @test
     */
    public function testArraySet()
    {
        $array = [];
        array_set('service.location.locale', 'en', $array);
        array_set('service.location.name', 'myservice', $array);
        array_set('service.namespace', 'myserviceNS', $array);

        $this->assertTrue(isset($array['service']));
        $this->assertTrue(isset($array['service']['namespace']));
        $this->assertTrue(isset($array['service']['location']));
        $this->assertTrue(isset($array['service']['location']));
        $this->assertTrue(isset($array['service']['location']['locale']));
        $this->assertTrue(isset($array['service']['location']['name']));
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

        $this->assertEquals(['obj-a', 'obj-b'], array_pluck('name', [$objectA, $objectB]));
        $this->assertEquals(['obj-a', 'obj-b', 'array'], array_pluck('name', [$objectA, $objectB, $array]));
    }

    /**
     * @test
     */
    public function testArrayNumeric()
    {
        $this->assertTrue(array_numeric([1, 2, 3]));
        $this->assertFalse(array_numeric([1, 'foo' => 'bar', 3]));
    }

    /**
     * @test
     */
    public function testArrayCompact()
    {
        $this->assertEquals([1, 2, 'a', 'b'], array_compact([1, '', 2, false, 'a', null, 'b']));
        $this->assertEquals(['foo' => true, 'bar' => 1], array_compact(['foo' => true, 'boo' => 0, 'bar' => 1]));
    }

    /**
     * @test
     */
    public function testArrayZip()
    {
        $this->assertEquals([['a', 'A', 1], ['b', 'B', 2], ['c', 'C', 3]], array_zip(['a', 'b', 'c'], ['A', 'B', 'C'], [1, 2, 3]));
    }

    /**
     * @test
     */
    public function testArrayMax()
    {
        $this->assertEquals(3, array_max([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /**
     * @test
     */
    public function testArrayMin()
    {
        $this->assertEquals(2, array_min([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /**
     * @test
     */
    public function testStrCamelCase()
    {
        $this->assertEquals('fooBar', str_camel_case('foo_bar'));
    }

    /**
     * @test
     */
    public function testStrCamelCaseAll()
    {
        $this->assertEquals('FooBar', str_camel_case_all('foo_bar'));
    }

    /**
     * @test
     */
    public function testStrLowDash()
    {
        $this->assertEquals('foo_bar', str_low_dash('fooBar'));
        $this->assertEquals('foo_bar_baz', str_low_dash('fooBarBaz'));
    }

    /**
     * @test
     */
    public function testStrStartsWith()
    {
        $this->assertTrue(str_starts_with('str', 'string'));
        $this->assertFalse(str_starts_with('sdr', 'string'));
    }

    /**
     * @test
     */
    public function testStrIStartsWith()
    {
        $this->assertTrue(stri_starts_with('str', 'String'));
        $this->assertTrue(stri_starts_with('Str', 'string'));
    }

    /**
     * @test
     */
    public function testStrEndsWith()
    {
        $this->assertTrue(str_ends_with('ing', 'string'));
        $this->assertFalse(str_ends_with('ink', 'string'));
    }

    /**
     * @test
     */
    public function testStriEndsWith()
    {
        $this->assertTrue(stri_ends_with('ING', 'string'));
        $this->assertTrue(stri_ends_with('ing', 'STRING'));
    }

    /**
     * @test
     */
    public function testStrContains()
    {
        $this->assertTrue(str_contains('rin', 'string'));
    }

    /**
     * @test
     */
    public function testSubstrAfter()
    {
        $this->assertEquals('doodle', substr_after('=', '--env=doodle'));
        $this->assertEquals('doodle=foo', substr_after('=', '--env=doodle=foo'));
    }

    /**
     * @test
     */
    public function testSubstriAfter()
    {
        $this->assertEquals('DAACD', substri_after('c', 'ABCDAACD'));
    }

    /**
     * @test
     */
    public function testSubstrBefore()
    {
        $this->assertEquals('ABC', substr_before('D', 'ABCDAACD'));
        $this->assertFalse('ABC' == substr_before('d', 'ABCDAACD'));
        $this->assertFalse(substr_before('x', 'ABCDAACD'));
    }

    /**
     * @test
     */
    public function testSubstriBefore()
    {
        $this->assertEquals('ABC', substri_before('D', 'ABCDAACD'));
        $this->assertEquals('ABC', substri_before('d', 'ABCDAACD'));
    }

    /**
     * @test
     */
    public function testStrRepeat()
    {
        $obj = $this->getMock('MyObject', array('__toString'));
        $obj->expects($this->any())->method('__toString')->will($this->returnValue('foo'));

        $this->assertEquals('foo bar', str_concat('foo', ' ', 'bar'));
        $this->assertEquals('foo bar', str_concat($obj, ' ', 'bar'));
    }

    /**
     * @test
     */
    public function testClearValue()
    {
        $this->assertNull(clear_value(''));
        $this->assertNull(clear_value(' '));
        $this->assertNull(clear_value(null));
        $this->assertFalse(is_null(clear_value(0)));
        $this->assertFalse(is_null(clear_value(false)));
        $this->assertFalse(is_null(clear_value([])));
    }

    /**
     * @test
     */
    public function testEquals()
    {
        $this->assertTrue(equals('1', 1));
        $this->assertTrue(equals('1', '1'));
    }

    /**
     * @test
     */
    public function testSame()
    {
        $objA = m::mock(__NAMESPACE__.'\\Foo');
        $objB = m::mock(__NAMESPACE__.'\\Foo');
        $objC = $objA;

        $this->assertFalse(same('1', 1));
        $this->assertTrue(same('1', '1'));
        $this->assertFalse(same($objA, $objB));
        $this->assertFalse(same($objC, $objB));
        $this->assertTrue(same($objA, $objC));
    }

    /**
     * @test
     */
    public function testContainedAndStartsWith()
    {
        $this->assertTrue(contained_and_starts_with(['foo', 'baz', 'bar'], 'fooBar'));
        $this->assertFalse(contained_and_starts_with(['foo', 'baz', 'bar'], 'FooBar'));
    }

    /**
     * @test
     */
    public function testContainedAndEndsWith()
    {
        $this->assertTrue(contained_and_ends_with(['foo', 'baz', 'bar'], 'Barfoo'));
        $this->assertFalse(contained_and_ends_with(['foo', 'baz', 'bar'], 'BarFoo'));
    }
}
