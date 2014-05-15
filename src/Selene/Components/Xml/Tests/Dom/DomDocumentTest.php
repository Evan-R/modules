<?php

/**
 * This File is part of the Selene\Components\Xml\Tests\Dom package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml\Tests\Dom;

use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;

/**
 * @class DomDocumentTest
 * @package Selene\Components\Xml\Tests\Dom
 * @version $Id$
 */
class DomDocumentTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\DOMDocument', new DOMDocument);
        $this->assertInstanceof('\Selene\Components\Xml\Dom\DOMDocument', new DOMDocument);
    }

    /** @test */
    public function itShouldCreateDOMElementsWithRightClass()
    {
        $dom = new DOMDocument;
        $element = $dom->createElement('foo', 'bar');

        $this->assertInstanceof('\Selene\Components\Xml\Dom\DOMElement', $element);
    }

    /** @test */
    public function itShouldReturnRightClassWhenIteratingOverDomNodeList()
    {
        $xml = '<data><foo>foo</foo><bar>bar</bar></data>';
        $dom = new DOMDocument;
        $dom->loadXML($xml, LIBXML_NONET);

        foreach ($dom->xpath('//foo|//bar') as $node) {
            $this->assertInstanceof('\Selene\Components\Xml\Dom\DOMElement', $node);
        }
    }
}
