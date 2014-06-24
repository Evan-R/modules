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

use \Selene\Components\Config\Validator\Nodes\FloatNode;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;
use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class FloatNodeTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FloatNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new FloatNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldValidateItsType()
    {
        $node = new FloatNode;
        $node->setKey('Node');

        $this->assertTrue($node->validate(8.1));

        try {
            $node->validate(8);
        } catch (InvalidTypeException $e) {
            $this->assertSame('Node needs to be type of double, instead saw integer', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldValidateAgainstMinimum()
    {
        $node = new FloatNode;
        $node->min(5.0);

        $this->assertTrue($node->validate(8.0));

        try {
            $node->validate(2.0);
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
        $node = new FloatNode;
        $node->max(5.0);

        $this->assertTrue($node->validate(4.9));

        try {
            $node->validate(6.1788);
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
        $node = new FloatNode;
        $node->min(4.9);
        $node->max(11.6);

        $this->assertTrue($node->validate(8.0));

        try {
            $node->validate(11.7);
        } catch (\OutOfRangeException $e) {
            $this->assertSame('value must be within the range of 4.9 and 11.6', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }
}
