<?php

/**
 * This File is part of the Selene\Components\Common\Tests\Helper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests\Helper;

use \Selene\Components\Common\Helper\StringHelper;

/**
 * @class StringHelperTest
 * @package Selene\Components\Common\Tests\Helper
 * @version $Id$
 */
class StringHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldSafeCompareStrings()
    {
        $a = 'secret';
        $b = 'secret';

        $this->assertTrue(StringHelper::strSafeCompare($a, $b));

        $a = 'secret';
        $b = 'somesecretkey';

        $this->assertFalse(StringHelper::strSafeCompare($a, $b));

        $a = 'secret';
        $b = 'secrut';

        $this->assertFalse(StringHelper::strSafeCompare($a, $b));
    }

    /** @test */
    public function itShouldTellIfStringEqulasString()
    {

        $a = 'string';
        $b = 'string';

        $this->assertTrue(StringHelper::strEquals($a, $b));

        $a = 'string';
        $b = 'otherstring';

        $this->assertFalse(StringHelper::strEquals($a, $b));
    }

    /**
     * @test
     */
    public function testStrCamelCase()
    {
        $this->assertEquals('fooBar', StringHelper::strCamelCase('fooBar'));
    }

    /**
     * @test
     */
    public function testStrCamelCaseAll()
    {
        $this->assertEquals('FooBar', StringHelper::strCamelCaseAll('foo_bar'));
    }

    /**
     * @test
     */
    public function testStrLowDash()
    {
        $this->assertEquals('foo_bar', StringHelper::strLowDash('fooBar'));
        $this->assertEquals('foo_bar_baz', StringHelper::strLowDash('fooBarBaz'));
    }

    /**
     * @test
     */
    public function testStrStartsWith()
    {
        $this->assertTrue(StringHelper::strStartsWith('string', 'str'));
        $this->assertFalse(StringHelper::strStartsWith('string', 'sdr'));
    }

    /**
     * @test
     */
    public function testStrIStartsWith()
    {
        $this->assertTrue(StringHelper::striStartsWith('String', 'str'));
        $this->assertTrue(StringHelper::striStartsWith('String', 'Str'));
    }

    /**
     * @test
     */
    public function testStrEndsWith()
    {
        $this->assertTrue(StringHelper::strEndsWith('string', 'ing'));
        $this->assertFalse(StringHelper::strEndsWith('string', 'ink'));
    }

    /**
     * @test
     */
    public function testStriEndsWith()
    {
        $this->assertTrue(StringHelper::striEndsWith('string', 'ING'));
        $this->assertTrue(StringHelper::striEndsWith('STRING', 'ing'));
    }

    /**
     * @test
     */
    public function testStrContains()
    {
        $this->assertTrue(StringHelper::strContains('string', 'rin'));
    }

    /**
     * @test
     */
    public function testSubstrAfter()
    {
        $this->assertEquals('doodle', StringHelper::substrAfter('--env=doodle', '='));
        $this->assertEquals('doodle=foo', StringHelper::substrAfter('--env=doodle=foo', '='));
    }

    /**
     * @test
     */
    public function testSubstriAfter()
    {
        $this->assertEquals('DAACD', StringHelper::substriAfter('ABCDAACD', 'c'));
    }

    /**
     * @test
     */
    public function testSubstrBefore()
    {
        $this->assertEquals('ABC', StringHelper::substrBefore('ABCDAACD', 'D'));
        $this->assertFalse(StringHelper::substrBefore('ABCDAACD', 'd'));
        $this->assertFalse(StringHelper::substrBefore('ABCDAACD', 'x'));
    }

    /**
     * @test
     */
    public function testSubstriBefore()
    {
        $this->assertEquals('ABC', StringHelper::substriBefore('ABCDAACD', 'D'));
        $this->assertEquals('ABC', StringHelper::substriBefore('ABCDAACD', 'd'));
    }

    /**
     * @test
     */
    public function testStrConcat()
    {
        $obj = $this->getMock('MyObject', array('__toString'));
        $obj->expects($this->any())->method('__toString')->will($this->returnValue('foo'));

        $this->assertEquals('foo bar', StringHelper::strConcat('foo', ' ', 'bar'));
        $this->assertEquals('foo bar', StringHelper::strConcat($obj, ' ', 'bar'));
    }

    /**
     * @test
     */
    public function testStrEscapeStr()
    {
        $this->assertSame('%%foo%%', StringHelper::strEscape('%foo%', '%'));
        $this->assertSame('this is @@foo@@ and %bar%', StringHelper::strEscape('this is @foo@ and %bar%', '@'));
    }

    /**
     * @test
     */
    public function testStrUnescapeStr()
    {
        $this->assertSame('%foo%', StringHelper::strUnescape('%%foo%%', '%'));
        $this->assertSame('this is @foo@ and %bar%', StringHelper::strUnescape('this is @@foo@@ and %bar%', '@'));
    }

    /**
     * @test
     */
    public function testContainedAndStartsWith()
    {
        //$this->assertTrue(containedAndStartsWith(['foo', 'baz', 'bar'], 'fooBar'));
        //$this->assertFalse(containedAndStartsWith(['foo', 'baz', 'bar'], 'FooBar'));
    }

    /**
     * @test
     */
    public function testContainedAndEndsWith()
    {
        //$this->assertTrue(containedAndEndsWith(['foo', 'baz', 'bar'], 'Barfoo'));
        //$this->assertFalse(containedAndEndsWith(['foo', 'baz', 'bar'], 'BarFoo'));
    }

    /**
     * @test
     * @dataProvider strRandLengthProvider
     */
    public function testStrRand($length)
    {
        $this->assertSame($length, strlen(StringHelper::strRand($length)));
    }

    /**
     * @test
     * @dataProvider strRandLengthProvider
     */
    public function testStrQuickRand($length)
    {
        $this->assertSame($length, strlen(StringHelper::strQuickRand($length)));
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
