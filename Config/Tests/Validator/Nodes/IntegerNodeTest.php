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
 */
class IntegerNodeTest extends RangeableNodeTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new IntegerNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldUseItsDefaultValueIfNull()
    {
        $node = $this->newNode();
        $node->defaultValue(12);
        $node->setKey('Node');

        $node->finalize();
        $this->assertTrue($node->validate());
    }

    /**
     * invalidTypesProvider
     *
     * @return array
     */
    public function validTypeProvider()
    {
        return [
            [0],
            [127],
            [0xfff]
        ];
    }

    /**
     * invalidTypesProvider
     *
     * @return array
     */
    public function invalidTypesProvider()
    {
        return [
            [[]],
            [''],
            ['12'],
            [1.2],
            [true],
            [false]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function minValueProvider()
    {
        return [
            [2, 3, 1],
            [4, 1200, 3],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function maxValueProvider()
    {
        return [
            [100, 99, 101],
            [4, 2, 5],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rangeValueProvider()
    {
        return [
            [[0, 5], 5, -1],
            [[-100, 100], -99, -101],
            [[-100, 100], 99, 101]
        ];
    }


    public function nodeDefaultValueProvier()
    {
        return [
            [1]
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getNodeClass()
    {
        return 'Selene\Components\Config\Validator\Nodes\IntegerNode';
    }
}
