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

use \Selene\Components\Config\Validator\Nodes\BooleanNode;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;

/**
 * @class BooleanNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class BooleanNodeTest extends NodeTest
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new BooleanNode;
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
            [true],
            [false]
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
            [12],
            [1.2],
            [0xfff],
        ];
    }

    protected function getNodeClass()
    {
        return 'Selene\Components\Config\Validator\Nodes\BooleanNode';
    }
}
