<?php

/**
 * This File is part of the Selene\Module\Xml\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Tests;

use \Selene\Module\Xml\Dom\DOMDocument;
use \Selene\Module\Xml\SimpleXMLElement;

/**
 * @class simpleXMLElementTest
 * @package Selene\Module\Xml\Tests
 * @version $Id$
 */
class SimpleXmlElementTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\SimpleXMLElement', new SimpleXMLElement('<data></data>'));
    }

    /** @test */
    public function itShouldParseAttributesAsArray()
    {
        $data =
        '<data id="12" entry="foo">
        </data>';
        $xml = new SimpleXMLElement($data);

        $this->assertSame(['id' => 12, 'entry' => 'foo'], $xml->attributesAsArray());
    }

    /** @test */
    public function itShouldConvertToPhpValues()
    {
        $xml = new SimpleXMLElement('<node>12</node>');

        $this->assertTrue(is_int($xml->phpValue()));

        $xml = new SimpleXMLElement('<node>12.1</node>');

        $this->assertTrue(is_float($xml->phpValue()));

        $xml = new SimpleXMLElement('<node>0xff0000</node>');

        $this->assertTrue(is_int($xml->phpValue()));

        $xml = new SimpleXMLElement('<node>true</node>');

        $this->assertTrue($xml->phpValue());

        $xml = new SimpleXMLElement('<node>false</node>');

        $this->assertFalse($xml->phpValue());
    }

    /** @test */
    public function itShouldAppendCdataSections()
    {

        $xml = new SimpleXMLElement('<node></node>');

        $xml->addCDATASection('string');

        $this->assertXmlStringEqualsXmlString('<node><![CDATA[string]]></node>', $xml->asXML());

        $xml = new SimpleXMLElement('<node></node>');
        $xml->addCDATASection(new \SimpleXMLElement('<data>string</data>'));

        $this->assertXmlStringEqualsXmlString('<node><![CDATA[<data>string</data>]]></node>', $xml->asXML());

        $xml = new SimpleXMLElement('<node></node>');
        $dom = new \DOMDocument;
        $dom->loadXML('<data>string</data>');
        $xml->addCDATASection($dom);

        $this->assertXmlStringEqualsXmlString('<node><![CDATA[<data>string</data>]]></node>', $xml->asXML());

        $xml = new SimpleXMLElement('<node></node>');
        $dom = new DOMDocument;
        $dom->loadXML('<root><data>string</data></root>');
        $xml->addCDATASection($dom->xpath('data')->item(0));

        $this->assertXmlStringEqualsXmlString('<node><![CDATA[<data>string</data>]]></node>', $xml->asXML());


        $xml = new SimpleXMLElement('<node></node>');

        try {
            $xml->addCDATASection([]);
        } catch (\InvalidArgumentException $e) {
            return $this->assertTrue(true);
        }
    }

    /** @test */
    public function itShouldAppendHTMLString()
    {
        $xml = new SimpleXMLElement('<node></node>');

        $xml->appendChildFromHtmlString('<a href="#">link</a>');

        $this->assertXmlStringEqualsXmlString('<node><a href="#">link</a></node>', $xml->asXML());
    }


    /** @test */
    public function itShouldAppendXmlStrings()
    {
        $xml = new SimpleXMLElement('<node></node>');
        $xml->appendChildFromXmlString('<foo>bar</foo>');

        $this->assertXmlStringEqualsXmlString('<node><foo>bar</foo></node>', $xml->asXML());
    }

    /** @test */
    public function itShouldAppendChildNodes()
    {
        $xml = new SimpleXMLElement('<node></node>');
        $xml->appendChildNode(new \SimpleXMLElement('<foo>bar</foo>'));

        $this->assertXmlStringEqualsXmlString('<node><foo>bar</foo></node>', $xml->asXML());
    }
}
