<?php

/**
 * This File is part of the Selene\Components\Xml\Tests\Normalizer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Tests\Normalizer;

use \Mockery as m;

use \Selene\Components\Xml\Normalizer\Normalizer;
use \Selene\Components\Xml\Normalizer\NormalizerInterface;
use \Selene\Components\Xml\Tests\Normalizer\Stubs\ArrayableStub;
use \Selene\Components\Xml\Tests\Normalizer\Stubs\ConvertToArrayStub;
use \Selene\Components\Xml\Tests\Normalizer\Stubs\SinglePropertyStub;
use \Selene\Components\Xml\Tests\Normalizer\Stubs\NestedPropertyStub;
use \Selene\Components\Xml\Tests\Normalizer\Stubs\TraversableStub;

/**
 * @class NormalizerTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Xml\Tests\Normalizer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class NormalizerTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $normalizer = new Normalizer;
        $this->assertInstanceof('\Selene\Components\Xml\Normalizer\Normalizer', $normalizer);
    }

    public function stringProvider()
    {
        return [
            ['foo-bar', 'fooBar'],
            ['foo-bar', 'foo_bar'],
            ['foo-bar', 'foo:bar'],
            ['foo-bar', 'foo.bar'],
            ['foo', '_foo'],
            ['foo', '%foo']
        ];
    }

    /**
     * @test
     * @dataProvider stringProvider
     */
    public function itShouldNormalizeInputToExpectedValue($expected, $value)
    {
        $normalizer = new Normalizer;

        $this->assertEquals($expected, $normalizer->normalize($value));
    }

    /**
     * @test
     */
    public function testConvertObjectToArray()
    {
        $normalizer = new Normalizer;

        $object = new ConvertToArrayStub;
        $data   = $normalizer->ensureArray($object);

        $this->assertEquals(array('foo' => 'foo', 'bar' => 'bar'), $data);
    }

    /** @test */
    public function itShouldConvertObjectToArrayExtened()
    {
        $normalizer = new Normalizer;

        $data = array('foo' => new SinglePropertyStub);
        $normalized = $normalizer->ensureArray($data);

        $this->assertEquals(array('foo' => array('baz' => 'bazvalue')), $normalized);
    }

    /** @test */
    public function isShouldConvertNestedObjectProperties()
    {
        $normalizer = new Normalizer;

        $data = array('foo' => new NestedPropertyStub);
        $normalized = $normalizer->ensureArray($data);

        $this->assertEquals(['foo' => ['baz' => ['foo' => 'foo', 'bar' => 'bar']]], $normalized);
    }

    /** @test */
    public function isShouldIgnoreIgnoredObjects()
    {
        $normalizer = new Normalizer;

        $normalizer->setIgnoredObjects((array)'\Selene\Components\Xml\Tests\Normalizer\Stubs\NestedPropertyStub');
        $data = ['foo' => ['bar' => new NestedPropertyStub]];
        $normalized = $normalizer->ensureArray($data);

        $this->assertEquals(['foo' => []], $normalized);
    }

    /** @test */
    public function isShouldConvertArrayableObjectToArray()
    {
        $normalizer = new Normalizer;

        $data = ['foo' => 'foo', 'bar' => 'bar'];
        $object = new ArrayableStub($data);
        $this->assertEquals($data, $normalizer->ensureArray($object));
    }

    /** @test */
    public function itShouldConvertObjectToArrayAndIgnoreRecursion()
    {
        $normalizer = new Normalizer;

        $data = ['bar' => 'bar', 'foo' => []];

        $objectA = new ConvertToArrayStub();

        $foo = [$objectA];
        $objectA->setFoo($foo);

        $out = $normalizer->ensureArray($objectA);
        $this->assertEquals([], $out['foo']);
    }

    /** @test */
    public function itShouldConvertArrayableObjectToArrayAndIgnoreAttributes()
    {
        $normalizer = new Normalizer;

        $normalizer->setIgnoredAttributes(['foo']);

        $data = array('foo' => 'foo', 'bar' => 'bar');
        $object = new ArrayableStub($data);

        $this->assertEquals(['bar' => 'bar'], $normalizer->ensureArray($data));
        $this->assertEquals(['bar' => 'bar'], $normalizer->ensureArray($object));

        $normalizer->addIgnoredAttribute('bar');

        $this->assertEquals([], $normalizer->ensureArray($data));
    }


    /** @test */
    public function itShouldIgnoreIgnoredClasses()
    {
        $normalizer = new Normalizer;

        $normalizer->setIgnoredAttributes(['foo']);

        $data = new \StdClass;
        $data->foo = 'bar';
        $data->bar = 'baz';

        $this->assertEquals(['bar' => 'baz'], $normalizer->ensureArray($data));
    }

    /** @test */
    public function itShouldConvertObjects()
    {
        $data = new \DOMDocument;
        $data->loadXML('<data><foo>bar</foo></data>');

        $normalizer = new Normalizer;
        $this->assertSame($data, $normalizer->ensureArray($data));


        $data = new TraversableStub($asset = ['foo' => 'bar', 'bam' => 'baz']);

        $this->assertSame($asset, $normalizer->ensureArray($data));
    }

    /** @test */
    public function itShouldConvertObjectsAndIgnoreCallableProperties()
    {
        $obj = new ConvertToArrayStub;

        $this->assertTrue(is_callable($obj->getBaz(), 'stub getter should return callable'));

        $normalizer = new Normalizer;
        $this->assertEquals(['foo' => 'foo', 'bar' => 'bar'], $normalizer->ensureArray($obj));
    }

    /** @test */
    public function itShouldIgnoredNonArrayables()
    {
        $normalizer = new Normalizer;
        $this->assertNull($normalizer->ensureArray('string'));
    }

    /** @test */
    public function itShouldEnsureBuildable()
    {
        $normalizer = new Normalizer;
        $xml = new \DOMDocument;

        $data = new TraversableStub($asset = ['foo' => 'bar', 'bam' => 'baz']);

        $this->assertSame($xml, $normalizer->ensureBuildable($xml));
        $this->assertSame($asset, $normalizer->ensureBuildable($data));
        $this->assertNull($normalizer->ensureBuildable(null));
    }

    /**
     * tearDown
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        m::close();
    }
}
