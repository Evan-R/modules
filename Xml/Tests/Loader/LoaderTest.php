<?php

/**
 * This File is part of the Selene\Module\Xml\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Tests\Loader;

use \Selene\Module\Xml\Loader\Loader;

/**
 * @class XmlLoaderTest
 * @package Selene\Module\Xml\Tests
 * @version $Id$
 */
class LoaderTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Module\Xml\Loader\LoaderInterface', new Loader);
    }

    /** @test */
    public function itShouldLoadXmlFiles()
    {
        $file = $this->getFixure();

        $loader = new Loader;

        $xml = $loader->load($file);
        $this->assertInstanceof('Selene\Module\Xml\Dom\DOMDocument', $xml);

        $this->assertInstanceof('DOMDocument', $xml);

        $loader->setOption(Loader::SIMPLEXML, true);

        $xml = $loader->load($file);
        $this->assertInstanceof('Selene\Module\Xml\SimpleXMLElement', $xml);
    }

    /** @test */
    public function itShouldLoadXmlStrings()
    {
        $loader = new Loader;
        $loader->setOption(Loader::FROM_STRING, true);

        $xml = $loader->load('<data></data>');
        $this->assertInstanceof('DOMDocument', $xml);
    }

    /** @test */
    public function domClassesShouldBeSettable()
    {

        $file = $this->getFixure();

        $loader = new Loader;
        $loader->setOption(Loader::DOM_CLASS, 'DOMDocument');

        $xml = $loader->load($file);

        $this->assertFalse($xml instanceof \Selene\Module\Xml\Dom\DOMDocument);
        $this->assertInstanceof('DOMDocument', $xml);
    }

    /** @test */
    public function simpleXmlClassesShouldBeSettable()
    {

        $file = $this->getFixure();

        $loader = new Loader;

        $loader->setOption(Loader::SIMPLEXML, true);
        $loader->setOption(Loader::SIMPLEXML_CLASS, 'SimpleXMLElement');

        $xml = $loader->load($file);

        $this->assertFalse($xml instanceof \Selene\Module\Xml\SimpleXmlElement);
        $this->assertInstanceof('SimpleXMLElement', $xml);
    }

    /** @test */
    public function loadingInvalidXmlShouldThrowExcepton()
    {
        $file = $this->getFixure();

        $loader = new Loader;
        $loader->setOption(Loader::FROM_STRING, true);

        try {
            $loader->load('<data><invalid></data>');
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test failed');
    }

    /** @test */
    public function cloningShouldResetOptions()
    {
        $loader = new Loader;
        $loader->setOption(Loader::FROM_STRING, true);

        $this->assertTrue($loader->getOption(Loader::FROM_STRING));

        $loader = clone($loader);

        $this->assertNull($loader->getOption(Loader::FROM_STRING));
    }

    /**
     * get the fixure file
     *
     * @access protected
     * @return string
     */
    protected function getFixure()
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.'Fixures'.DIRECTORY_SEPARATOR.'test.xml';
    }
}
