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

use \Selene\Components\Config\Validator\Exception\LengthException;
use \Selene\Components\Config\Validator\Exception\RangeException;

/**
 * @class NumericNodeTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
abstract class RangeableNodeTest extends NodeTest
{
    /**
     * @test
     * @dataProvider minValueProvider
     */
    public function itShouldValidateAgainstMinimum($min, $value, $invalid)
    {
        $node = $this->newNode();
        $node->min($min);
        $node->finalize($value);

        $this->assertTrue($node->validate());

        $node = $this->newNode();
        $node->min($min);
        $node->finalize($invalid);

        try {
            $node->validate();
        } catch (LengthException $e) {
            $this->assertSame('Value must not deceed '.$min.'.', $e->getMessage());

            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test slipped');
    }

    /**
     * @test
     * @dataProvider maxValueProvider
     */
    public function itShouldValidateAgainstMaximum($max, $value, $invalid)
    {
        $node = $this->newNode();
        $node->max($max);
        $node->finalize($value);

        $this->assertTrue($node->validate());

        $node = $this->newNode();
        $node->max($max);
        $node->finalize($invalid);

        try {
            $node->validate();
        } catch (LengthException $e) {
            $this->assertSame('Value must not exceed '.$max.'.', $e->getMessage());

            return;
        } catch (\Exception $e) {

            $this->fail($e->getMessage());
        }

        $this->fail('test slipped');
    }

    /**
     * @test
     * @dataProvider rangeValueProvider
     */
    public function itShouldValidateAgainstARange(array $range, $value, $invalid)
    {
        list ($min, $max) = $range;

        $node = $this->newNode();
        $node->min($min);
        $node->max($max);

        $node->finalize($value);

        $this->assertTrue($node->validate());

        $node = $this->newNode();
        $node->min($min);
        $node->max($max);

        $node->finalize($invalid);

        try {
            $node->validate();
        } catch (RangeException $e) {
            $this->assertSame(
                'Value must be within the range of '.$min.' and '.$max.'.',
                $e->getMessage()
            );

            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test slipped');
    }

    /**
     * Provies values for the minimum test.
     *
     * @return array
     */
    abstract public function minValueProvider();

    /**
     * Provies values for the maximum test.
     *
     * @return array
     */
    abstract public function maxValueProvider();

    /**
     * Provies values for the range test.
     *
     * @return array
     */
    abstract public function rangeValueProvider();
}
