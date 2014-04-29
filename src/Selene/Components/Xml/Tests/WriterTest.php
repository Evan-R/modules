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
use \Selene\Components\Xml\Writer;
use \Selene\Components\Xml\Normalizer\Normalizer;
use \Selene\Components\Xml\Normalizer\NormalizerInterface;

/**
 * @class WriterTest
 * @package Selene\Components\Xml\Tests
 * @version $Id$
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $writer = new Writer(m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));
        $this->assertInstanceof('\Selene\Components\Xml\Writer', $writer);
    }

    /** @test */
    public function itSouldDumpAnXmlString()
    {
        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->with([])->andReturn([]);
        $xml = $writer->dump([]);

        $this->assertXmlStringEqualsXmlString('<root></root>', $xml);

        $n->shouldReceive('ensureBuildable')->with($args = ['bar' => 'baz'])->andReturn($args);

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $xml = $writer->dump($args, 'foo');
        $this->assertXmlStringEqualsXmlString('<foo><bar>baz</bar></foo>', $xml);
    }

    /** @test */
    public function itShouldWriteToADOMDocument()
    {
        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->with([])->andReturn([]);
        $xml = $writer->writeToDom([]);

        $this->assertInstanceof('DOMDocument', $xml);
    }

    /** @test */
    public function itShouldInflectPlurals()
    {
        $args = [
            'tags' => ['mysql', 'postgres']
        ];
        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $writer->setInflector(function ($value) {
            return strrpos($value, 's') === (strlen($value) - 1) ? substr($value, 0, -1) : $value;
        });

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root><tags><tag>mysql</tag>
            <tag>postgres</tag></tags></root>',
            $xml
        );
    }

    /** @test */
    public function itShouldMappAttributes()
    {
        $args = [
            'foo' => ['id' => 10, 'val' => 'value']
        ];

        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $writer->addMappedAttribute('foo', 'id');

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root><foo id="10"><val>value</val>
            </foo></root>',
            $xml
        );
    }

    /** @test */
    public function itShouldUseValueKeys()
    {
        $args = [
            'foo' => ['@attributes' => ['id' => 10], 'value' => 'value']
        ];

        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $writer->useKeyAsValue('value');

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root><foo id="10">value</foo></root>',
            $xml
        );
    }

    /** @test */
    public function itShouldUseIndexKeys()
    {
        $args = [
            'foo' => [0, 1]
        ];

        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root><foo><item>0</item><item>1</item></foo></root>',
            $xml
        );

        $writer->useKeyAsIndex('i');

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root><foo><i>0</i><i>1</i></foo></root>',
            $xml
        );
    }

    /** @test */
    public function itShouldConvertXmlElements()
    {

        $args = [
            'foo' => new \DOMElement('bar', 'baz')
        ];

        $writer = new Writer($n = m::mock('\Selene\Components\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root><foo><bar>baz</bar></foo></root>',
            $xml
        );
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
