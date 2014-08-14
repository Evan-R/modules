<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Validator\Nodes;

use \Mockery as m;
use \Selene\Module\Config\Validator\Nodes\MissingValue;

/**
 * @class MissingValueTest
 * @package Selene\Module\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class MissingValueTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Selene\Module\Config\Validator\Nodes\MissingValue',
            new MissingValue($this->mockNode())
        );
    }

    /** @test */
    public function itShouldReturnNull()
    {
        $this->assertNull((new MissingValue($this->mockNode()))->getValue());
    }

    /** @test */
    public function itShouldReturnNode()
    {
        $mv = new MissingValue($node = $this->mockNode());
        $this->assertSame($node, $mv->getNode());
    }

    protected function mockNode()
    {
        return m::mock('\Selene\Module\Config\Validator\Nodes\NodeInterface');
    }

    protected function tearDown()
    {
        m::close();
    }
}
