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

use \Selene\Module\Config\Validator\Exception\ValidationException;

/**
 * @class StringNodeTest
 * @package Selene\Module\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class StringNodeTest extends RangeableNodeTest
{

    /** @test */
    public function itShouldValidateAgainsRegexp()
    {
        $node = $this->newNode();
        $node->setKey('Node');
        $node->regexp('~\d+~');

        $node->finalize('test');

        $clone = clone($node);
        $clone->finalize('120');
        $this->assertTrue($clone->validate());

        try {
            $node->validate();
        } catch (ValidationException $e) {
            $this->assertSame('Node value "test" doesn\'t macht given pattern.', $e->getMessage());

            return;
        }

        $this->fail('test slipped');
    }

    /** @test */
    public function itShouldHaveRangeSetterAliases()
    {
        $node = $this->newNode();
        try {
            $node->lengthBetween(2, 10);

            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * invalidTypesProvider
     *
     * @return array
     */
    public function validTypeProvider()
    {
        return [
            ['string']
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
            [1.2],
            ['1.2'],
            [0xfff],
            [12],
            [true],
            [false]
        ];
    }

    public function nodeDefaultValueProvier()
    {
        return [
            ['str']
        ];
    }

    public function minValueProvider()
    {
        return [
            [2, '  ', ' '],
            [4, '    ', '   '],
        ];
    }

    public function maxValueProvider()
    {
        return [
            [4, 'oooo', 'ooooo'],
            [4, '', 'ooooo'],
        ];
    }

    public function rangeValueProvider()
    {
        return [
            [[2, 5], 'ooo', 'oooooo']
        ];
    }

    protected function getNodeClass()
    {
        return 'Selene\Module\Config\Validator\Nodes\StringNode';
    }
}
