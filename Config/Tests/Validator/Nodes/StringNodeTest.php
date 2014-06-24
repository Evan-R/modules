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

use \Selene\Components\Config\Validator\Nodes\StringNode;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;
use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class StringNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class StringNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new StringNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldValidateAgainsStrings()
    {
        $node = new StringNode;
        $this->assertTrue($node->validate('string'));

        try {
            $node->validate(100);
        } catch (\Exception $e) {
            $this->assertInstanceof('\Selene\Components\Config\Validator\Exception\InvalidTypeException', $e);
            return;
        }
        $this->fail();
    }

    /** @test */
    public function itShouldValidateAgainstMinLengthConstraints()
    {
        $node = new StringNode;
        $node->minLength(5);

        $this->assertTrue($node->validate('somestring'));

        try {
            $node->validate('ts');
        } catch (\LengthException $e) {
            $this->assertSame('value must not deceed a length of 5', $e->getMessage());
            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldValidateAgainstMaxLengthConstraints()
    {
        $node = new StringNode;
        $node->maxLength(5);

        $this->assertTrue($node->validate('12345'));

        try {
            $node->validate('123456');
        } catch (\LengthException $e) {
            $this->assertSame('value must not exceed a length of 5', $e->getMessage());
            return;
        }

        $this->fail('test slipped');
    }

    /** @test */
    public function itShouldValidateAgainstRangeConstraints()
    {
        $node = new StringNode;

        $node->minLength(5);
        $node->maxLength(10);

        $this->assertTrue($node->validate('abcde'));

        try {
            $node->validate('1');
        } catch (\OutOfRangeException $e) {
            $this->assertSame('value lenght must be within the range of 5 and 10', $e->getMessage());
            return;
        }

        $this->fail('test slipped');
    }

    /** @test */
    public function itShouldMatchAgaintsGivenRegexp()
    {
        $node = new StringNode;
        $node->regexp('~[0-9][a-z]~');

        $this->assertTrue($node->validate('0a'));

        try {
            $node->validate($p = '0A');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(sprintf('value %s doesn\'t macht given pattern', $p), $e->getMessage());
            return;
        }

        $this->fail('test slipped');
    }
}
