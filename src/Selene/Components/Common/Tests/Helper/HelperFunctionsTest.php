<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests\Helper;

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
    public function testArrayGetShouldReturnNullOnUnknowenKeys()
    {
        $this->assertNull(arrayGet(['foo' => ['bar' => 'baz']], 'baz.bar'));
        $this->assertNull(arrayGet(['foo' => ['bar' => 'baz']], 'fo.baz'));
    }

    /**
     * @test
     * @dataProvider arrayGetDataProvider
     */
    public function testArrayGet($query, $array, $expected)
    {
        $this->assertEquals($expected, arrayGet($array, $query));
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

    /**
     * @test
     */
    public function testArrayGetReturnInput()
    {
        $this->assertEquals(['a' => 'b'], arrayGet(['a' => 'b']));
        $this->assertEquals(['a' => 'b'], arrayGet(['a' => 'b']));
    }

    /**
     * @test
     */
    public function testArraySet()
    {
        $array = [];
        arraySet('foo', 'bar', $array);
        arraySet('service.location.locale', 'en', $array);
        arraySet('service.location.name', 'myservice', $array);
        arraySet('service.namespace', 'myserviceNS', $array);
        arraySet('service.location.0', 'in1', $array);
        arraySet('service.location.1', 'in2', $array);

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

        arraySet('foo', 'bar', $data);
        arraySet('baz', ['doo'], $data);
        arraySet('baz.some', 'goo', $data);
        arraySet('baz.glue', 'fuxk', $data);
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

        $this->assertEquals(['obj-a', 'obj-b'], arrayPluck('name', [$objectA, $objectB]));
        $this->assertEquals(['obj-a', 'obj-b', 'array'], arrayPluck('name', [$objectA, $objectB, $array]));
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

        $this->assertEquals(['12', '14', '22'], array_column($in, 'id'));
        $this->assertEquals(['xkd23' => '12', 'xkd25' => '14', 'xkd77' => '22'], array_column($in, 'id', 'handle'));
    }

    /**
     * @test
     */
    public function testArrayNumeric()
    {
        $this->assertTrue(arrayNumeric([1, 2, 3]));
        $this->assertFalse(arrayNumeric([1, 'foo' => 'bar', 3]));
    }

    /**
     * @test
     */
    public function testArrayCompact()
    {
        $this->assertEquals([1, 2, 'a', 'b'], arrayCompact([1, '', 2, false, 'a', null, 'b']));
        $this->assertEquals(['foo' => true, 'bar' => 1], arrayCompact(['foo' => true, 'boo' => 0, 'bar' => 1]));
    }

    /**
     * @test
     */
    public function testArrayZip()
    {
        $this->assertEquals(
            [['a', 'A', 1], ['b', 'B', 2], ['c', 'C', 3]],
            arrayZip(['a', 'b', 'c'], ['A', 'B', 'C'], [1, 2, 3])
        );
    }

    /**
     * @test
     */
    public function testArrayMax()
    {
        $this->assertEquals(3, arrayMax([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /**
     * @test
     */
    public function testArrayMin()
    {
        $this->assertEquals(2, arrayMin([['a', 'b', 'c'], ['A', 'C'], [1, 2, 3]]));
    }

    /**
     * @test
     */
    public function testStrCamelCase()
    {
        $this->assertEquals('fooBar', strCamelCase('fooBar'));
    }

    /**
     * @test
     */
    public function testStrCamelCaseAll()
    {
        $this->assertEquals('FooBar', strCamelCaseAll('foo_bar'));
    }

    /**
     * @test
     */
    public function testStrLowDash()
    {
        $this->assertEquals('foo_bar', strLowDash('fooBar'));
        $this->assertEquals('foo_bar_baz', strLowDash('fooBarBaz'));
    }

    /**
     * @test
     */
    public function testStrStartsWith()
    {
        $this->assertTrue(strStartsWith('str', 'string'));
        $this->assertFalse(strStartsWith('sdr', 'string'));
    }

    /**
     * @test
     */
    public function testStrIStartsWith()
    {
        $this->assertTrue(striStartsWith('str', 'String'));
        $this->assertTrue(striStartsWith('Str', 'string'));
    }

    /**
     * @test
     */
    public function testStrEndsWith()
    {
        $this->assertTrue(strEndsWith('ing', 'string'));
        $this->assertFalse(strEndsWith('ink', 'string'));
    }

    /**
     * @test
     */
    public function testStriEndsWith()
    {
        $this->assertTrue(striEndsWith('ING', 'string'));
        $this->assertTrue(striEndsWith('ing', 'STRING'));
    }

    /**
     * @test
     */
    public function testStrContains()
    {
        $this->assertTrue(strContains('rin', 'string'));
    }

    /**
     * @test
     */
    public function testSubstrAfter()
    {
        $this->assertEquals('doodle', substrAfter('=', '--env=doodle'));
        $this->assertEquals('doodle=foo', substrAfter('=', '--env=doodle=foo'));
    }

    /**
     * @test
     */
    public function testSubstriAfter()
    {
        $this->assertEquals('DAACD', substriAfter('c', 'ABCDAACD'));
    }

    /**
     * @test
     */
    public function testSubstrBefore()
    {
        $this->assertEquals('ABC', substrBefore('D', 'ABCDAACD'));
        $this->assertFalse('ABC' == substrBefore('d', 'ABCDAACD'));
        $this->assertFalse(substrBefore('x', 'ABCDAACD'));
    }

    /**
     * @test
     */
    public function testSubstriBefore()
    {
        $this->assertEquals('ABC', substriBefore('D', 'ABCDAACD'));
        $this->assertEquals('ABC', substriBefore('d', 'ABCDAACD'));
    }

    /**
     * @test
     */
    public function testStrRepeat()
    {
        $obj = $this->getMock('MyObject', array('__toString'));
        $obj->expects($this->any())->method('__toString')->will($this->returnValue('foo'));

        $this->assertEquals('foo bar', strConcat('foo', ' ', 'bar'));
        $this->assertEquals('foo bar', strConcat($obj, ' ', 'bar'));
    }

    /**
     * @test
     */
    public function testStrEscapeStr()
    {
        $this->assertSame('%%foo%%', strEscapeStr('%foo%', '%'));
        $this->assertSame('this is @@foo@@ and %bar%', strEscapeStr('this is @foo@ and %bar%', '@'));
    }

    /**
     * @test
     */
    public function testStrUnescapeStr()
    {
        $this->assertSame('%foo%', strUnescapeStr('%%foo%%', '%'));
        $this->assertSame('this is @foo@ and %bar%', strUnescapeStr('this is @@foo@@ and %bar%', '@'));
    }

    /**
     * @test
     */
    public function testClearValue()
    {
        $this->assertNull(clearValue(''));
        $this->assertNull(clearValue(' '));
        $this->assertNull(clearValue(null));
        $this->assertFalse(is_null(clearValue(0)));
        $this->assertFalse(is_null(clearValue(false)));
        $this->assertFalse(is_null(clearValue([])));
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
        $this->assertTrue(containedAndStartsWith(['foo', 'baz', 'bar'], 'fooBar'));
        $this->assertFalse(containedAndStartsWith(['foo', 'baz', 'bar'], 'FooBar'));
    }

    /**
     * @test
     */
    public function testContainedAndEndsWith()
    {
        $this->assertTrue(containedAndEndsWith(['foo', 'baz', 'bar'], 'Barfoo'));
        $this->assertFalse(containedAndEndsWith(['foo', 'baz', 'bar'], 'BarFoo'));
    }

    /**
     * @test
     * @dataProvider strRandLengthProvider
     */
    public function testStrRand($length)
    {
        $this->assertSame($length, strlen(strRand($length)));
    }

    /**
     * @test
     * @dataProvider strRandLengthProvider
     */
    public function testStrQuickRand($length)
    {
        $this->assertSame($length, strlen(strQuickRand($length)));
    }

    public function strRandLengthProvider()
    {
        return [
            [12],
            [22],
            [40],
            [25],
            [125]
        ];
    }
}
