<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator\Nodes;

use \Mockery as m;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;

/**
 * @class DictNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class DictNodeTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new DictNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldVaidateAssociativeArrays()
    {
        $node = new DictNode;
        $node->setKey('dict');
        $node->addChild($this->getNodeMock('foo', $node));
        $this->assertTrue($node->validate(['foo' => 'bar']));

        try {
            $node->validate(['bar', 'foo' => 'bar']);
        } catch (InvalidTypeException $e) {
            $this->assertSame('dict may not contain numeric keys', $e->getMessage());
            return;
        }

        $this->fail('test slipped');
    }

    /** @test */
    public function itShouldCheckExceedingInputValues()
    {
        $node = new DictNode;
        $node->setKey('dict');
        $node->addChild($this->getNodeMock('foo', $node));

        try {
            $node->validate(['foo' => 'bar', 'bar' => 'baz']);
        } catch (ValidationException $e) {
            $this->assertSame('invalid key bar in dict', $e->getMessage());
            return;
        }

        $this->fail('test slipped');

    }

    protected function getNodeMock($key, $parent)
    {
        $node = m::mock('Selene\Components\Config\Validator\Nodes\NodeInterface');
        $node->shouldReceive('setParent')->with($parent)->andReturn($node);
        $node->shouldReceive('getParent')->andReturn($parent);
        $node->shouldReceive('getKey')->andReturn($key);

        return $node;
    }
}
