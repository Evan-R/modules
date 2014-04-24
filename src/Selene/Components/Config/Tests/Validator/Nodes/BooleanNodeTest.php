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
class BooleanNodeTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new BooleanNode;
        $this->assertInstanceOf('Selene\Components\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShouldValidateAgainstInput()
    {
        $node = new BooleanNode;

        $this->assertTrue($node->validate(true));
        $this->assertTrue($node->validate(false));

        try {
            $node->validate('string');
        } catch (\Exception $e) {
            $this->assertInstanceof('\Selene\Components\Config\Validator\Exception\InvalidTypeException', $e);
        }
    }
}
