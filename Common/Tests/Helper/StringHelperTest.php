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

    /** @test */
    public function itShouldCamelCaseStrings()
    {
        $this->assertEquals('fooBar', StringHelper::strCamelCase('foo_bar'));
    }

    /** @test */
    public function strCamelCaseAll()
    {
        $this->assertEquals('FooBar', StringHelper::strCamelCaseAll('foo_bar'));
    }

    /** @test */
    public function itShouldSnakeCaseStrings()
    {
        $this->assertEquals('foo_bar', StringHelper::strLowDash('fooBar'));
        $this->assertEquals('foo_bar_baz', StringHelper::strLowDash('fooBarBaz'));
    }

    /** @test */
    public function itShouldSnakeCaseStringsUsingACustomDelimiter()
    {
        $this->assertEquals('foo@bar', StringHelper::strLowDash('fooBar', '@'));
        $this->assertEquals('foo:bar:baz', StringHelper::strLowDash('fooBarBaz', ':'));
    }

    /** @test */
    public function strStartsWith()
    {
        $this->assertTrue(StringHelper::strStartsWith('string', 'str'));
        $this->assertFalse(StringHelper::strStartsWith('string', 'sdr'));
    }

    /** @test */
    public function strIStartsWith()
    {
        $this->assertTrue(StringHelper::striStartsWith('String', 'str'));
        $this->assertTrue(StringHelper::striStartsWith('String', 'Str'));
    }

    /** @test */
    public function strEndsWith()
    {
        $this->assertTrue(StringHelper::strEndsWith('string', 'ing'));
        $this->assertFalse(StringHelper::strEndsWith('string', 'ink'));

        $this->assertTrue(StringHelper::strEndsWith('Foo\\Controller', 'Controller'));
    }

    /** @test */
    public function striEndsWith()
    {
        $this->assertTrue(StringHelper::striEndsWith('string', 'ING'));
        $this->assertTrue(StringHelper::striEndsWith('STRING', 'ing'));

        $this->assertTrue(StringHelper::striEndsWith('Controller\\Foo\\Controller', 'controller'));
    }

    /** @test */
    public function strContains()
    {
        $this->assertTrue(StringHelper::strContains('string', 'rin'));
    }

    /** @test */
    public function testSubstrAfter()
    {
        $this->assertEquals('doodle', StringHelper::substrAfter('--env=doodle', '='));
        $this->assertEquals('doodle=foo', StringHelper::substrAfter('--env=doodle=foo', '='));
    }

    /** @test */
    public function strNull()
    {
        $nullStr = '';
        $this->assertNull(StringHelper::strNull($nullStr));

        $str = 'foo';
        $this->assertSame($str, StringHelper::strNull($str));

        $inp = new \StdClass;
        $this->assertSame($inp, StringHelper::strNull($inp));
    }

    /** @test */
    public function testSubstriAfter()
    {
        $this->assertEquals('DAACD', StringHelper::substriAfter('ABCDAACD', 'c'));
    }

    /** @test */
    public function testSubstrBefore()
    {
        $this->assertEquals('ABC', StringHelper::substrBefore('ABCDAACD', 'D'));
        $this->assertFalse(StringHelper::substrBefore('ABCDAACD', 'd'));
        $this->assertFalse(StringHelper::substrBefore('ABCDAACD', 'x'));
    }

    /** @test */
    public function testSubstriBefore()
    {
        $this->assertEquals('ABC', StringHelper::substriBefore('ABCDAACD', 'D'));
        $this->assertEquals('ABC', StringHelper::substriBefore('ABCDAACD', 'd'));
    }

    /** @test */
    public function strConcat()
    {
        $obj = $this->getMock('MyObject', array('__toString'));
        $obj->expects($this->any())->method('__toString')->will($this->returnValue('foo'));

        $this->assertEquals('foo bar', StringHelper::strConcat('foo', ' ', 'bar'));
        $this->assertEquals('foo bar', StringHelper::strConcat($obj, ' ', 'bar'));
    }

    /** @test */
    public function strEscapeStr()
    {
        $this->assertSame('%%foo%%', StringHelper::strEscape('%foo%', '%'));
        $this->assertSame('this is @@foo@@ and %bar%', StringHelper::strEscape('this is @foo@ and %bar%', '@'));
    }

    /** @test */
    public function strUnescapeStr()
    {
        $this->assertSame('%foo%', StringHelper::strUnescape('%%foo%%', '%'));
        $this->assertSame('this is @foo@ and %bar%', StringHelper::strUnescape('this is @@foo@@ and %bar%', '@'));
    }

    /** @test */
    public function testContainedAndStartsWith()
    {
        //$this->assertTrue(containedAndStartsWith(['foo', 'baz', 'bar'], 'fooBar'));
        //$this->assertFalse(containedAndStartsWith(['foo', 'baz', 'bar'], 'FooBar'));
    }

    /** @test */
    public function testContainedAndEndsWith()
    {
        //$this->assertTrue(containedAndEndsWith(['foo', 'baz', 'bar'], 'Barfoo'));
        //$this->assertFalse(containedAndEndsWith(['foo', 'baz', 'bar'], 'BarFoo'));
    }

    /** @test */
    public function strRand($length)
    {
        $this->assertSame($length, strlen(StringHelper::strRand($length)));
    }

    /** @test */
    public function strQuickRand($length)
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
