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

use \Selene\Components\Config\Validator\Nodes\NodeInterface;
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
abstract class ArrayNodeTest extends NodeTest
{
    /** @test */
    public function typeShouldBeArray()
    {
        $this->assertSame(NodeInterface::T_ARRAY, $this->newNode()->getType());
    }
}
