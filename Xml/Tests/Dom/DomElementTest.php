<?php

/**
 * This File is part of the Selene\Module\Xml\Tests\Dom package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Tests\Dom;

use \Selene\Module\Xml\Dom\DOMElement;
use \Selene\Module\Xml\Dom\DOMDocument;

/**
 * @class DomElementTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Module\Xml\Tests\Dom
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DomElementTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\DOMElement', new DOMElement('foo'));
        $this->assertInstanceof('\Selene\Module\Xml\Dom\DOMElement', new DOMElement('foo'));
    }

    /** @test */
    public function xpathShouldThrowExceptionWithoutOwnerDocument()
    {
        $element = new DOMElement('foo');

        try {
            $element->xpath('//foo');
        } catch (\BadMethodCallException $e) {
            $this->assertSame('cannot xpath on element without an owner document', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function appendDomElementShouldThrowExceptionWithoutOwnerDocument()
    {
        $element = new DOMElement('foo');

        try {
            $element->appendDomElement(new DOMElement('bar'));
        } catch (\BadMethodCallException $e) {
            $this->assertSame('cannot add an element without an owner document', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
