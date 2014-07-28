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

use \Mockery as m;
use \Selene\Components\Config\Validator\Nodes\DictNode;
use \Selene\Components\Config\Validator\Nodes\StringNode;
use \Selene\Components\Config\Validator\Exception\ValidationException;
use \Selene\Components\Config\Validator\Exception\InvalidTypeException;

/**
 * @class DictNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class DictNodeTest extends ArrayNodeTest
{
    /** @test */
    public function itShoulBeIteratable()
    {
        $node = $this->newNode();
        $node->addChild($str = new StringNode);
        $str->setKey('value');

        $res = [];
        foreach ($node as $c) {
            $res[] = $c->getKey();
        }

        $this->assertSame(['value'], $res);
    }

    /** @test */
    public function itShouldInvalidateExceedingKeysWhenStrict()
    {
        $node = new DictNode(DictNode::KEYS_STRICT);
        $node->setKey('Node');

        $node->addChild($str = new StringNode);
        $str->setKey('value');

        $node->finalize(['value' => 'val']);

        $this->assertTrue($node->validate());

        $node = clone($node);
        $node->finalize(['value' => 'val', 'test' => 'foo']);

        try {
            $node->validate();
        } catch (ValidationException $e) {
            $this->assertSame('Invalid key "test" in Node.', $e->getMessage());

            return;
        }

        $this->fail('test slipped.');
    }

    /** @test */
    public function itShouldGetChildKeys()
    {
        $node  = $this->newNode();
        $first = new StringNode;
        $first->setKey('first');
        $last  = new StringNode;
        $last->setKey('last');

        $node->addChild($first);
        $node->addChild($last);

        $this->assertSame(['first', 'last'], $node->getKeys());
    }

    public function nodeDefaultValueProvier()
    {
        return [
            [['test' => 'ok']]
        ];
    }

    /**
     * invalidTypeProvider
     *
     * @return array
     */
    public function validTypeProvider()
    {
        return [
            [[]],
            [['foo' => 'bar']]
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
            [[0, 1, 2, 4]],
            ['string']
        ];
    }


    /**
     * {@inheritdoc}
     */
    protected function getNodeClass()
    {
        return 'Selene\Components\Config\Validator\Nodes\DictNode';
    }
}
