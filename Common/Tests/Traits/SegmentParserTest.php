<?php

/**
 * This File is part of the Selene\Module\Common\Tests\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Traits;

use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Common\Tests\Stubs\Traits\SegmentParserClass;

/**
 * @class SegmentParserTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Module\Common\Tests\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class SegmentParserTest extends TestCase
{
    /**
     * testParse
     *
     * @access public
     * @return mixed
     */
    public function testParse()
    {
        $parser = new SegmentParserClass();

        $this->assertSame([null, 'baz', null], $parser->parse('baz'));
        $this->assertSame([null, 'baz', 'bam'], $parser->parse('baz.bam'));
        $this->assertSame(['foo', 'bar', null], $parser->parse('foo::bar'));
        $this->assertSame(['foo', 'baz', null], $parser->parse('foo::baz'));
        $this->assertSame(['foo', 'bar', 'bam'], $parser->parse('foo::bar.bam'));
    }
}
