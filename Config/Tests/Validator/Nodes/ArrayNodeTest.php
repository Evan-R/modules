<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator\Nodes;

use \Selene\Components\Config\Tests\Validator\Stubs\NodeStub;
use \Selene\Components\Config\Tests\Validator\Stubs\ArrayNodeStub as ArrayNode;

/**
 * @class ArrayNodeTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ArrayNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new NodeStub;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldGetItsParent()
    {
        $node = new ArrayNode;
        $nodeB = new NodeStub;
        $nodeC = new NodeStub;

        $nodeB->setParent($node);
        $nodeC->setParent($node);

        $this->assertTrue($node->hasChildren(), 'Rootnode should have children');
        $this->assertTrue($nodeB->hasParent(), 'ChildNode B should have parent');
        $this->assertSame($nodeB, $node->getFirstChild(), 'first child of root should be $nodeB');
        $this->assertSame($nodeC, $node->getLastChild(), 'last child of root should be $nodeC');
        $this->assertSame($node, $nodeB->getParent(), 'parent node of $nodeB should be $node');

        $this->assertEquals([$nodeB, $nodeC], $node->getChildren());
    }

    /** @test */
    public function itShouldSetItsChild()
    {
        $node = new ArrayNode;
        $nodeB = new NodeStub;
        $nodeC = new NodeStub;

        $node->addChild($nodeB);
        $node->addChild($nodeC);

        $this->assertTrue($node->hasChildren(), 'Rootnode should have children');
        $this->assertTrue($nodeB->hasParent(), 'ChildNode B should have parent');
        $this->assertSame($nodeB, $node->getFirstChild(), 'first child of root should be $nodeB');
        $this->assertSame($nodeC, $node->getLastChild(), 'last child of root should be $nodeC');
        $this->assertSame($node, $nodeB->getParent(), 'parent node of $nodeB should be $node');

        $this->assertEquals([$nodeB, $nodeC], $node->getChildren());
    }

    /** @test */
    public function itShouldHaveChild()
    {
        $node = new ArrayNode;
        $childB = new NodeStub;
        $childC = new NodeStub;

        $this->assertFalse($node->hasChild($childB));
        $this->assertFalse($node->hasChild($childC));

        $node->addChild($childB);

        $this->assertTrue($node->hasChild($childB));
        $this->assertFalse($node->hasChild($childC));

        $node->addChild($childC);

        $this->assertTrue($node->hasChild($childC));
    }
}
