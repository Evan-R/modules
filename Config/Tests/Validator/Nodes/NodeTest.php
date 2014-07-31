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
use \Selene\Components\Config\Validator\Nodes\MissingValue;
use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;
use \Selene\Components\Config\Validator\Exception\MissingValueException;

/**
 * @class NodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
abstract class NodeTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     * @dataProvider validTypeProvider
     */
    public function itShouldValidateValidValues($value)
    {
        $node = $this->newNode();

        $node->finalize($value);
        $this->assertTrue($node->validate());
    }

    /**
     * @test
     * @dataProvider invalidTypesProvider
     */
    public function itShouldValidateItsType($value)
    {
        $node = $this->newNode();

        $node->setKey('Node');

        try {
            $node->finalize($value);
            $node->validate();
        } catch (InvalidTypeException $e) {
            $this->assertSame(
                'Node needs to be type of '.$node->getType().', instead saw '.gettype($value).'.',
                $e->getMessage()
            );
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @test
     * @expectedException BadMethodCallException
     */
    public function itShouldThrowOnValidationIfNotFinalized()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->validate();
    }

    /** @test */
    public function itShouldBeAllowedToBeEmptyByDefault()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->finalize(null);

        $this->assertTrue($node->validate());
    }

    /**
     * @test
     * @expectedException Selene\Components\Config\Validator\Exception\MissingValueException
     */
    public function itShouldThrowIfValueIsNotOptionalAndMissing()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->setRequired(true);
        $node->finalize(new MissingValue($node));

        $this->assertTrue($node->validate());
    }

    /**
     * @test
     */
    public function itShouldNotThrowIfValueIsOptionalAndMissing()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->setRequired(false);
        $node->finalize(new MissingValue($node));

        $this->assertTrue($node->validate());
    }

    /**
     * @test
     * @expectedException Selene\Components\Config\Validator\Exception\ValidationException
     */
    public function itShouldThrowIfValueIsNotOptionalButEmpty()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->notEmpty();
        $node->setRequired(true);
        $node->finalize(null);

        $this->assertTrue($node->validate());
    }

    /** @test */
    public function itShouldGetItsKeyFormatted()
    {
        $parent = new DictNode;
        $parent->setKey('parent');

        $node = $this->newNode();
        $node->setKey('child');

        $node->setParent($parent);

        $this->assertSame('parent[child]', $node->getFormattedKey());
    }

    /**
     * @test
     * @dataProvider nodeDefaultValueProvier
     */
    public function itShouldBeResetAfterCloning($value)
    {
        $node = $this->newNode();
        $node->finalize($value);
        $this->assertSame($value, $node->getValue());

        $clone = clone($node);
        $this->assertNull($clone->getValue());
        $clone->finalize($value);
        $this->assertSame($value, $clone->getValue());
    }

    /** @test */
    public function testCloneCondition()
    {
        $node = $this->newNode();
        $c = $node->condition();
        $c->when(function () {
            return false;
        })->then(function () {
        });

        $clone = clone($node);

        $this->assertSame($node, $c->end());
        $this->assertSame($clone, current($clone->getConditions())->end());
    }

    /**
     * @test
     * @dataProvider nodeDefaultValueProvier
     */
    public function itShouldAddConditions($value)
    {
        $node = $this->newNode();
        $node->condition()
            ->when(function ($val) {
                return null === $val;
            })
            ->then(function () use ($value) {
                return $value;
            })
            ->end();

        $node->finalize(null);

        $this->assertSame($value, $node->getValue());

    }

    /**
     * @test
     * @dataProvider nodeDefaultValueProvier
     */
    public function itShouldRemoveNode($value)
    {
        // should unset the value
        $parent = new DictNode;
        $node = $this->newNode();
        $node->condition()
            ->when(function () {
                return true;
            })
            ->thenRemove()->end();

        $node->setParent($parent);
        $this->assertTrue($node->hasParent());
        $node->finalize($value);

        $this->assertNull($node->getValue());
        $this->assertFalse($node->hasParent(), 'Node should be detached from parent node.');
    }

    /**
     * @test
     * @dataProvider nodeDefaultValueProvier
     */
    public function itShouldBeInvalidated($value)
    {
        // should unset the value
        $node = $this->newNode();
        $node->setKey('node');
        $node->condition()
            ->when(function () {
                return true;
            })
            ->thenMarkInvalid()->end();

        $node->finalize($value);

        try {
            $node->validate();
        } catch (ValidationException $e) {
            $value = is_scalar($value) ? (string)$value : json_encode($value);
            $this->assertSame('node: Invalid value '.$value, $e->getMessage());

            return;
        }

        $this->fail('test slipped');
    }

    /**
     * validTypeProvider
     *
     * @return array
     */
    abstract public function validTypeProvider();

    /**
     * invalidTypesProvider
     *
     * @return array
     */
    abstract public function invalidTypesProvider();

    abstract public function nodeDefaultValueProvier();

    /**
     * newNode
     *
     * @param array $args
     *
     * @return NodeInterace
     */
    protected function newNode(array $args = [])
    {
        $class = new \ReflectionClass($this->getNodeClass());

        return $class->newInstanceArgs($args);
    }

    protected function prepareNode(NodeInterface $node, $value)
    {
        $node->finalize($value);
    }

    /**
     * getNodeClass
     *
     * @return string
     */
    abstract protected function getNodeClass();
}
