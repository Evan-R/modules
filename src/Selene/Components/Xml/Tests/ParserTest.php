<?php

/**
 * This File is part of the Selene\Components\Xml\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Tests;

use \Mockery as m;
use \Selene\Components\Xml\Parser;
use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\Xml\Loader\LoaderInterface;

/**
 * @class ParserTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Xml\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $parser = new Parser(m::mock('\Selene\Components\Xml\Loader\LoaderInterface'));
        $this->assertInstanceof('\Selene\Components\Xml\Parser', $parser);

        $parser = new Parser;
        $this->assertInstanceof('\Selene\Components\Xml\Parser', $parser);
    }

    /** @test */
    public function itShouldParseAXmlString()
    {
        $xml = '<root><data>test</data></root>';
        $parser = new Parser;

        $data = $parser->parse($xml);

        $this->assertEquals(['root' => ['data' => 'test']], $data);
    }

    /** @test */
    public function itShouldParseAXmlFile()
    {
        $file = __DIR__.DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'test.xml';
        $parser = new Parser;
        $data = $parser->parse($file);

        $this->assertEquals(['root' => ['data' => 'test']], $data);
    }

    /** @test */
    public function itShouldParseDom()
    {
        $parser = new Parser;

        $dom = new DOMDocument;
        $element = $dom->createElement('foo');
        $element->appendDomElement(new DOMElement('bar', 'baz'));
        $dom->appendChild($element);

        $this->assertEquals(['foo' => ['bar' => 'baz']], $parser->parseDom($dom));
    }

    /** @test */
    public function itShouldParseDomElements()
    {
        $parser = new Parser;

        $dom = new DOMDocument;
        $element = $dom->createElement('foo');
        $element->appendDomElement(new DOMElement('bar', 'baz'));

        $this->assertEquals(['bar' => 'baz'], $parser->parseDomElement($element));
    }

    /** @test */
    public function itShouldThrowExceptionIfDOMisEmpty()
    {
        $parser = new Parser;
        $dom = new DOMDocument;

        try {
            $parser->parseDom($dom);
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('DOM has no root element', $e->getMessage());
            return;
        }

        $this->fail('you lose');
    }

    /** @test */
    public function itShouldRespectPluralizedParentKeys()
    {
        $parser = new Parser;

        $parser->setPluralizer(function ($string) {
            return $string . 's';
        });


        $xmlString = '<data><aas><aa>1</aa></aas></data>';

        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['aas' => [1]]], $data);

        $parser->setIndexKey('item');

        $xmlString = '<data><aas><item>1</item></aas></data>';

        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['aas' => [1]]], $data);
    }

    /** @test */
    public function itShouldParseAttributesAsKeys()
    {
        $parser = new Parser;
        $parser->setMergeAttributes(true);

        $xmlString = '<data><foo id="1">bar</foo></data>';
        $data = $parser->parse($xmlString);
        $this->assertEquals(['data' => ['foo' => ['id' => 1, 'value' => 'bar']]], $data);
    }

    /** @test */
    public function itShouldParseAttributesAsArray()
    {
        $parser = new Parser;
        $parser->setMergeAttributes(false);

        $xmlString = '<data><foo id="1">bar</foo></data>';
        $data = $parser->parse($xmlString);
        $this->assertEquals(['data' => ['foo' => ['@attributes' => ['id' => 1], 'value' => 'bar']]], $data);
    }

    /** @test */
    public function attributeKeyNameShouldBeSettable()
    {
        $parser = new Parser;
        $parser->setMergeAttributes(false);
        $parser->setAttributesKey('attrs');

        $xmlString = '<data><foo id="1">bar</foo></data>';
        $data = $parser->parse($xmlString);
        $this->assertEquals(['data' => ['foo' => ['attrs' => ['id' => 1], 'value' => 'bar']]], $data);
    }

    /** @test */
    public function itIsSetInnerTextAsValueKey()
    {
        $parser = new Parser;

        $xmlString = '<data><foo>bar<inner>foo</inner></foo></data>';
        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['foo' => ['inner' => 'foo', 'value' => 'bar']]], $data);
    }

    /** @test */
    public function itShouldNormalizeKeys()
    {
        $parser = new Parser;
        $parser->setKeyNormalizer(function ($key) {
            return strtr($key, ['-' => '_']);
        });

        $xmlString = '<data><test-name>foo</test-name></data>';
        $data = $parser->parse($xmlString);
        $this->assertEquals(['data' => ['test_name' => 'foo']], $data);
    }

    /** @test */
    public function itShouldHandleRegularDomDocuments()
    {
        $parser = new Parser;

        $dom = new \DOMDocument;
        $dataNode = $dom->createElement('data');
        $dom->appendChild($dataNode);

        $data = $parser->parseDom($dom);

        $this->assertEquals(['data' => null], $data);
    }

    /** @test */
    public function itShouldParseItemsAndRespectArrayNotaion()
    {

        $xmlString =
        '<data>
            <items>
                <item>a</item>
                <item>b</item>
                <item>c</item>
            </items>
        </data>';

        $parser = new Parser;
        $parser->setIndexKey('item');

        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['items' => ['a', 'b', 'c']]], $data);

        $parser = new Parser;
        $parser->setPluralizer(function ($string) {
            return $string . 's';
        });

        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['items' => ['a', 'b', 'c']]], $data);
    }

    /** @test */
    public function itShouldContinueIndexOnParsingMixedListStructure()
    {
        $xmlString =
        '<data>
            <items>
                <item>a</item>
                <item>b</item>
                <item>c</item>
                <bla>d</bla>
                <item>e</item>
            </items>
        </data>';

        $parser = new Parser;
        $parser->setPluralizer(function ($string) {
            return $string . 's';
        });

        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['items' => ['a', 'b', 'c', 'bla' => 'd', 'e']]], $data);

        $parser = new Parser;

        $data = $parser->parse($xmlString);

        $this->assertEquals(['data' => ['items' => ['item' => ['a', 'b', 'c', 'e'], 'bla' => 'd']]], $data);
    }

    /** @test */
    public function staticHelperTest()
    {
        $dom = new DOMDocument;

        $elementParent = $dom->createElement('data');
        $elementChild = $dom->createElement('foo', 'bar');
        $elementParent->appendChild($elementChild);

        $this->assertEquals(['foo' => 'bar'], Parser::getPhpValue($elementParent));
        $this->assertNull(Parser::getPhpValue(''));
        $this->assertSame(10, Parser::getPhpValue('10'));
        $this->assertSame(1.2, Parser::getPhpValue('1.2'));
        $this->assertTrue(Parser::getPhpValue('true'));
        $this->assertFalse(Parser::getPhpValue('false'));
    }

    /**
     * tearDown
     *
     * @access protected
     * @return mixed
     */
    protected function tearDown()
    {
        m::close();
    }
}
