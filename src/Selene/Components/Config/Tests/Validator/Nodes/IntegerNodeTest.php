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

use \Selene\Components\Config\Validator\Nodes\IntegerNode;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;
use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class IntegerNodeTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IntegerNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new IntegerNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldValidateItsType()
    {
        $node = new IntegerNode;
        $node->setKey('Node');

        $this->assertTrue($node->validate(8));

        try {
            $node->validate(8.1);
        } catch (InvalidTypeException $e) {
            $this->assertSame('Node needs to be type of integer, instead saw double', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldValidateAgainstMinimum()
    {
        $node = new IntegerNode;
        $node->min(5);

        $this->assertTrue($node->validate(8));

        try {
            $node->validate(2);
        } catch (\LengthException $e) {
            $this->assertSame('value must not deceed 5', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldValidateAgainstMaximum()
    {
        $node = new IntegerNode;
        $node->max(5);

        $this->assertTrue($node->validate(5));

        try {
            $node->validate(6);
        } catch (\LengthException $e) {
            $this->assertSame('value must not exceed 5', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldValidateAgainstRanges()
    {
        $node = new IntegerNode;
        $node->min(5);
        $node->max(10);

        $this->assertTrue($node->validate(8));

        try {
            $node->validate(11);
        } catch (\OutOfRangeException $e) {
            $this->assertSame('value must be within the range of 5 and 10', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
