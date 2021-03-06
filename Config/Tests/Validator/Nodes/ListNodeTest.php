<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Validator\Nodes;

use \Mockery as m;
use \Selene\Module\Config\Validator\Nodes\DictNode;
use \Selene\Module\Config\Validator\Nodes\ListNode;
use \Selene\Module\Config\Validator\Nodes\StringNode;
use \Selene\Module\Config\Validator\Exception\ValidationException;
use \Selene\Module\Config\Validator\Exception\InvalidTypeException;

/**
 * @class ListNodeTest
 * @package Selene\Module\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class ListNodeTest extends ArrayNodeTest
{
    /** @test */
    public function itShouldGetFistAndLastChild()
    {
        $node  = $this->newNode();
        $node->setKey('List');
        $first = new StringNode;
        $first->setKey('first');

        $node->addChild($first);

        $last  = new StringNode;
        $last->setKey('last');

        try {
            $node->addChild($last);
        } catch (\BadMethodCallException $e) {
            $this->assertSame('ListNode List already as a node declaration.', $e->getMessage());
        }

        $this->assertSame($first, $node->getFirstChild());
        $this->assertSame($first, $node->getLastChild());
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
            if (!is_array($value)) {
                $this->assertSame(
                    'Node needs to be type of '.$node->getType().', instead saw '.gettype($value).'.',
                    $e->getMessage()
                );
            } else {
                $this->assertTrue(0 === strpos($e->getMessage(), 'Invalid key'));
            }

            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /** @test */
    public function itShouldTreatChildNodeAsBlueprint()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->notEmpty();
        $node->addChild($str = new StringNode);

        $node->finalize(['', 12]);

        try {
            $node->validate();
        } catch (ValidationException $e) {
            $this->assertSame('Node[1] needs to be type of string, instead saw integer.', $e->getMessage());
        }

        $node = $this->newNode();
        $node->setKey('Node');
        $node->notEmpty();
        $node->addChild($dict = new DictNode);

        $dict->addChild($str = new StringNode);
        $str->notEmpty()->setKey('test');

        $node->finalize([['test' => '']]);

        try {
            $node->validate();
        } catch (ValidationException $e) {
            $this->assertSame('Node[0][test] may not be empty.', $e->getMessage());
        }
    }

    public function nodeDefaultValueProvier()
    {
        return [
            [[1, 2]]
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
            [[], []],
            [[1, 2, 4]]
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
            [['dict' => 'value']],
            ['string'],
            [1],
            [1.0],
            [true]
        ];
    }


    /**
     * {@inheritdoc}
     */
    protected function getNodeClass()
    {
        return 'Selene\Module\Config\Validator\Nodes\ListNode';
    }
}
