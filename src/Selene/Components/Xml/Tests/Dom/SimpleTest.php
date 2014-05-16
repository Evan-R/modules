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

use \DOMDocument as DOM;
use \Selene\Components\Xml\Dom\DOMDocument;

class SimpleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @target hhvm
     * will fail because it will return the original base named class
     * `DOMElement` instead of `Selene\Components\Xml\Dom\DOMElement`
     *
     * It will work if the DOMElement class name differs from the interal class
     * name
     */
    public function itShouldReturnExtendDomElementOnNativeDOM()
    {
        $dom = new DOM;
        $dom->registerNodeClass('DOMElement', 'Selene\Components\Xml\Dom\DOMElement');

        $test = $dom->createElement('test');
        $this->assertInstanceof('Selene\Components\Xml\Dom\DOMElement', $test);
    }

    /** @test */
    public function itShouldReturnExtendDomElementOnExtendedDOM()
    {
        $dom = new DOMDocument;
        $dom->registerNodeClass('DOMElement', 'Selene\Components\Xml\Dom\DOMElement');

        $test = $dom->createElement('test');
        $this->assertInstanceof('Selene\Components\Xml\Dom\DOMElement', $test);
    }
}
