<?php

/**
 * This File is part of the Yam\Utils\Tests\Data package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Tests\Data;

use \Selene\Module\Common\Data\BaseList;

/**
 * @class BaseListTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Module\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class BaseListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function testConstructWithData()
    {
        $list = new BaseList(1, 2, 3, 4, 5);
        $this->assertEquals(5, count($list));
        $this->assertEquals([1, 2, 3, 4, 5], $list->toArray());
    }

    /**
     * @test
     */
    public function testPop()
    {
        $list = new BaseList(1, 2, 3, 4, 5);

        $this->assertEquals(5, $list->pop());
        $this->assertEquals(2, $list->pop(1));
        $this->assertEquals(4, $list->pop(2));
    }

    /**
     * @test
     */
    public function popShouldThrowErrorOnInvalidIndex()
    {
        $list = new BaseList(1, 2);

        try {
            $this->assertEquals(4, $list->remove(3));
        } catch (\InvalidArgumentException $e) {
            $this->assertTrue(true);
            return;
        }
        $this->fail();
    }

    /**
     * @test
     */
    public function testInsert()
    {
        $list = new BaseList(1, 2, 3, 4, 5);

        $list->insert(3, 'foo');
        $this->assertEquals([1, 2, 3, 'foo', 4, 5], $list->toArray());
    }


    /**
     * @test
     */
    public function testCountValue()
    {
        $list = new BaseList(1, 'red', 'green', 3, 'blue', 4, 'red', 5);

        $this->assertEquals(2, $list->countValue('red'));
        $this->assertEquals(1, $list->countValue('green'));
    }

    /**
     * @test
     */
    public function testSort()
    {
        $list = new BaseList(120, -1, 3, 20, -110);

        $list->sort();

        $this->assertEquals([-110, -1, 3, 20, 120], $list->toArray());
    }

    /**
     * @test
     */
    public function testRemove()
    {
        $list = new BaseList(1, 2, 3, 4, 5);

        $list->remove(3);

        $this->assertEquals([1, 2, 4, 5], $list->toArray());

        $list = new BaseList('red', 'green', 'blue');

        $list->remove('green');

        $this->assertEquals(['red', 'blue'], $list->toArray());
    }

    /**
     * @test
     */
    public function testReverse()
    {
        $list = new BaseList(1, 2, 3, 4, 5);
        $list->reverse();

        $this->assertEquals([5, 4, 3, 2, 1], $list->toArray());
    }

    /**
     * @test
     */
    public function testExtend()
    {
        $listA = new BaseList(1, 2, 3, 4, 5);
        $listB = new BaseList('red', 'green');

        $listA->extend($listB);

        $this->assertEquals([1, 2, 3, 4, 5, 'red', 'green'], $listA->toArray());
    }
}
