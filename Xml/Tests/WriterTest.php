<?php

/*
 * This File is part of the Selene\Module\Xml\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Tests;

use \Mockery as m;
use \Selene\Module\Xml\Writer;
use \Selene\Module\Xml\Normalizer\Normalizer;
use \Selene\Module\Xml\Normalizer\NormalizerInterface;

/**
 * @class WriterTest
 * @package Selene\Module\Xml\Tests
 * @version $Id$
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstantiable()
    {
        $writer = new Writer(m::mock('\Selene\Module\Xml\Normalizer\NormalizerInterface'));
        $this->assertInstanceof('\Selene\Module\Xml\Writer', $writer);
    }

    /** @test */
    public function itSouldDumpAnXmlString()
    {
        $writer = new Writer($n = m::mock('\Selene\Module\Xml\Normalizer\NormalizerInterface'));

        $n->shouldReceive('ensureBuildable')->with([])->andReturn([]);
        $xml = $writer->dump([]);

        $this->assertXmlStringEqualsXmlString('<root></root>', $xml);

        $n->shouldReceive('ensureBuildable')->with($args = ['bar' => 'baz'])->andReturn($args);

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $xml = $writer->dump($args, 'foo');
        $this->assertXmlStringEqualsXmlString(
            '<foo>
                <bar>baz</bar>
            </foo>',
            $xml
        );
    }

    /** @test */
    public function itShouldWriteToADOMDocument()
    {
        $writer = new Writer($n = m::mock('\Selene\Module\Xml\Normalizer\NormalizerInterface'));

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

        $writer = new Writer($this->getNormalizerMock());

        $writer->setInflector(function ($value) {
            return strrpos($value, 's') === (strlen($value) - 1) ? substr($value, 0, -1) : $value;
        });

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <tags>
                    <tag>mysql</tag>
                    <tag>postgres</tag>
                </tags>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldMappAttributes()
    {
        $args = [
            'foo' => ['id' => 10, 'val' => 'value']
        ];

        $writer = new Writer($this->getNormalizerMock());

        $writer->addMappedAttribute('foo', 'id');

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo id="10">
                    <val>value</val>
                </foo>
            </root>',
            $xml
        );

        $writer = new Writer($this->getNormalizerMock());

        $args = [
            'bar' => ['id' => 10, 'val' => 'value']
        ];

        $writer->addMappedAttribute('*', 'id');
        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <bar id="10">
                    <val>value</val>
                </bar>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldMappAttributesFromAttributesMap()
    {
        $args = [
            'foo' => ['soma' => true, 'val' => 'value']
        ];

        $writer = new Writer($this->getNormalizerMock());

        $writer->setAttributeMap([
            'foo' => ['soma']
        ]);

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo soma="true">
                    <val>value</val>
                </foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldIgnoreInvalidAttributeContent()
    {
        $args = [
            'foo' => ['id' => [1, 2]]
        ];

        $writer = new Writer($this->getNormalizerMock());

        $writer->setAttributeMap([
          'foo' => ['id']
        ]);

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <id>
                        <item>1</item>
                        <item>2</item>
                    </id>
                </foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldAddTypeToStringTypes()
    {
        $args = [
            'foo' => ['value' => '2']
        ];

        $writer = new Writer($this->getNormalizerMock());

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <value type="string">2</value>
                </foo>
            </root>',
            $xml
        );

        $args = [
            'foo' => ['value' => 'true']
        ];

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <value type="string">true</value>
                </foo>
            </root>',
            $xml
        );

        $args = [
            'foo' => ['value' => '<a>link</a>']
        ];

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <value><![CDATA[<a>link</a>]]></value>
                </foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldUseValueKeys()
    {
        $args = [
            'foo' => ['@attributes' => ['id' => 10], 'value' => 'value']
        ];

        $writer = new Writer($this->getNormalizerMock());

        $writer->useKeyAsValue('value');

        $xml = $writer->dump($args);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo id="10">value</foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldUseIndexKeys()
    {
        $args = [
            'foo' => [0, 1]
        ];

        $writer = new Writer($this->getNormalizerMock());

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <item>0</item>
                    <item>1</item>
                </foo>
            </root>',
            $xml
        );

        $writer->useKeyAsIndex('i');

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <i>0</i>
                    <i>1</i>
                </foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldShouldUseParentKeyAsIndexIfNoneSpecified()
    {
        $writer = new Writer($this->getNormalizerMock());

        $writer->useKeyAsIndex(null);

        $args = [
            'foo' => [0, 1]
        ];

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>0</foo>
                <foo>1</foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldConvertXmlElements()
    {
        $args = [
            'foo' => new \DOMElement('bar', 'baz')
        ];

        $writer = new Writer($this->getNormalizerMock());

        $xml = $writer->dump($args);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>
                    <bar>baz</bar>
                </foo>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldNotParseInvalidKeyNames()
    {
        $writer = new Writer($this->getNormalizerMock());

        try {
            $writer->useKeyAsIndex($str = '%%adssad');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame($str . ' is an invalid node name', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('failed');

    }

    /** @test */
    public function itShouldChangeThisTestName()
    {
        $writer = new Writer($this->getNormalizerMock());

        try {
            $writer->useKeyAsValue($str = '%%adssad');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame($str . ' is an invalid node name', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('failed');

    }

    /** @test */
    public function itShouldGetCorretBooleans()
    {
        $data = ['foo' => true, 'bar' => false];
        $writer = new Writer($this->getNormalizerMock());

        $xml = $writer->dump($data);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>true</foo>
                <bar>false</bar>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldGetCorrectNumbers()
    {
        $data = ['foo' => 12, 'bar' => 1.2];
        $writer = new Writer($this->getNormalizerMock());

        $xml = $writer->dump($data);

        $this->assertXmlStringEqualsXmlString(
            '<root>
                <foo>12</foo>
                <bar>1.2</bar>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itIsExpectedThat()
    {
        $writer = new Writer($this->getNormalizerMock());
        $xml = $writer->dump(null);

        $this->assertXmlStringEqualsXmlString('<root></root>', $xml);

        $xml = $writer->dump('foo');

        $this->assertXmlStringEqualsXmlString('<root>foo</root>', $xml);
    }

    /** @test */
    public function itShouldParseSimpleXmlObjects()
    {
        $writer = new Writer($this->getNormalizerMock());
        $xml = simplexml_load_string('<foo>bar</foo>');

        $data = ['data' => $xml];

        $xml = $writer->dump($data);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <data>
                    <foo>bar</foo>
                </data>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itShouldDoWiredStuff()
    {
        $dom = new \DOMDocument;
        $el = $dom->createElement('foo', 'bar');
        $dom->appendChild($el);

        $writer = new Writer($this->getNormalizerMock());

        $data = ['slam' => $dom];

        $xml = $writer->dump($data);
        $this->assertXmlStringEqualsXmlString(
            '<root>
                <slam>
                    <foo>bar</foo>
                </slam>
            </root>',
            $xml
        );
    }

    /** @test */
    public function itIsExpectedItWillIgnoreEmptyNodes()
    {
        $writer = new Writer(new Normalizer);

        $data = ['test' => ['foo' => null]];

        $xml = $writer->dump($data, 'data');

        $this->assertXmlStringEqualsXmlString('<data><test/></data>', $xml);
    }

    protected function getNormalizerMock()
    {
        $n = m::mock('\Selene\Module\Xml\Normalizer\NormalizerInterface');

        $n->shouldReceive('ensureBuildable')->andReturnUsing(function ($arg) {
            return $arg;
        });

        $n->shouldReceive('normalize')->andReturnUsing(function ($arg) {
            return $arg;
        });

        return $n;
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
