<?php

/**
 * This File is part of the Selene\Components\Config\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\Config\SequenceParser;

/**
 * @class SequenceParserTest
 * @package
 * @version $Id$
 */
class SequenceParserTest extends TestCase
{
    protected $parser;

    protected function setUp()
    {
        parent::setUp();
        $this->parser = new SequenceParser;
    }

    public function testParseSequence()
    {
        $this->assertEquals(['foo', 'bar', 'baz'], $this->parser->parseSequence('foo::bar.baz'));
        $this->assertEquals([null, 'bar', 'baz'], $this->parser->parseSequence('bar.baz'));
        $this->assertEquals([null, 'bar', null], $this->parser->parseSequence('bar'));
        $this->assertEquals(['foo', 'bar', 'baz.bam'], $this->parser->parseSequence('foo::bar.baz.bam'));
    }
}
