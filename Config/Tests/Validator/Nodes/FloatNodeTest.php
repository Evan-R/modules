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
use \Selene\Components\Config\Validator\Exception\RangeException;
use \Selene\Components\Config\Validator\Exception\LengthException;

/**
 * @class FloatNodeTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FloatNodeTest extends RangeableNodeTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new FloatNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /**
     * invalidTypesProvider
     *
     * @return array
     */
    public function validTypeProvider()
    {
        return [
            [0.1],
            [127.55]
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
            ['1.2'],
            [0xfff],
            [12],
            [true],
            [false]
        ];
    }

    public function minValueProvider()
    {
        return [
            [2.0, 2.01, 1.99],
            [4.0, 4.01, 3.99],
        ];
    }

    public function maxValueProvider()
    {
        return [
            [100.0, 99.999, 100.1],
            [4.0, 3.9, 4.01],
        ];
    }

    public function rangeValueProvider()
    {
        return [
            [[0.0, 5.0], 5.0, 6.0]
        ];
    }

    public function nodeDefaultValueProvier()
    {
        return [
            [1.0]
        ];
    }

    protected function getNodeClass()
    {
        return 'Selene\Components\Config\Validator\Nodes\FloatNode';
    }
}
