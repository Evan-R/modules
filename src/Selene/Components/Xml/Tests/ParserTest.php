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
