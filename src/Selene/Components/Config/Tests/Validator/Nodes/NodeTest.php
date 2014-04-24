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

use \Selene\Components\Config\Tests\Validator\Stubs\NodeStub;

class NodeTest extends \PHPUnit_Framework_TestCase
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
        $node = new NodeStub;
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
        $node = new NodeStub;
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
}
