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

use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Tests\Validator\Stubs\NodeStub as Node;

/**
 * @class GeneralNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class GeneralNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldTrowExceptionIfCallingAMethodOnNoneExistingBuilder()
    {
        $parent = new DictNode();
        $parent->setKey('parent');

        $node = new Node;
        $node->setKey('child');
        $node->setParent($parent);

        try {
            $node->end();
        } catch (\BadMethodCallException $e) {
            $this->assertSame('Node parent[child]: no builder set or method end() does not exist.', $e->getMessage());
        }
    }
}
