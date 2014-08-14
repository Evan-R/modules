<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Validator\Nodes;

use \Selene\Module\Config\Validator\Nodes\StringNode;
use \Selene\Module\Config\Validator\Nodes\NodeInterface;
use \Selene\Module\Config\Tests\Validator\Stubs\NodeStub;
use \Selene\Module\Config\Tests\Validator\Stubs\ArrayNodeStub as ArrayNode;

/**
 * @abstract class ArrayNodeTest extends NodeTest
 * @see NodeTest
 * @abstract
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class ArrayNodeTest extends NodeTest
{
    /** @test */
    public function typeShouldBeArray()
    {
        $this->assertSame(NodeInterface::T_ARRAY, $this->newNode()->getType());
    }

    /** @test */
    public function itShouldAddAndRemoveChildren()
    {
        $node  = $this->newNode();
        $child = new StringNode;

        $node->addChild($child);
        $this->assertSame($node, $child->getParent());

        $node->removeChild($child);
        $this->assertFalse($node->hasChild($child));
    }

    /** @test */
    public function itShouldGetFistAndLastChild()
    {
        $node  = $this->newNode();
        $first = new StringNode;
        $first->setKey('first');
        $last  = new StringNode;
        $last->setKey('last');

        $node->addChild($first);
        $node->addChild($last);

        $this->assertSame($first, $node->getFirstChild());
        $this->assertSame($last, $node->getLastChild());
    }
}
