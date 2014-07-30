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
use \Selene\Components\Config\Validator\Nodes\Condition;
use \Selene\Components\Config\Validator\Exception\ValidationException;

/**
 * @class ConditionTest
 * @package Selene\Components\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class ConditionTest extends \PHPUnit_Framework_TestCase
{

    protected $node;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\Condition',
            new Condition($this->node)
        );
    }

    /** @test */
    public function simpleConditions()
    {
        $cnd = $this->newCondition();

        $cnd->when(function () {
            return true;
        })->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd);
    }

    /** @test */
    public function isInArray()
    {
        $cnd = $this->newCondition();

        $cnd->ifInArray([1, 2, 3])->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd, [2]);
    }

    /** @test */
    public function isNotInArray()
    {
        $cnd = $this->newCondition();

        $cnd->ifNotInArray([1, 2, 3])->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd, [12]);
    }

    /** @test */
    public function ifIsArray()
    {
        $cnd = $this->newCondition();

        $cnd->ifisArray()->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd, [[]]);
    }

    /** @test */
    public function ifNotArray()
    {
        $cnd = $this->newCondition();

        $cnd->ifisNotArray()->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd, [1]);
    }

    /** @test */
    public function ifIsString()
    {
        $cnd = $this->newCondition();

        $cnd->ifIsString()->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd, ['str']);
    }

    /** @test */
    public function ifIsNull()
    {
        $cnd = $this->newCondition();

        $cnd->ifIsNull()->then(function () {
            throw new \BadMethodCallException('under test.');
        });

        return $this->execCallback($cnd, [null]);
    }

    /** @test */
    public function itShouldReturnEmptyArray()
    {
        $cnd = $this->newCondition();

        $cnd->when(function () {
            return true;
        })->thenEmptyArray();

        $this->assertSame([], $cnd->run());
    }

    /** @test */
    public function markConditionInvalid()
    {
        $cnd = $this->newCondition();

        $cnd->when(function () {
            return true;
        })->thenMarkInvalid('Invalidate');

        try {
            call_user_func($cnd->getResult(), '');
        } catch (ValidationException $e) {
            $this->assertSame('Invalidate', $e->getMessage());

            return;
        }
    }

    /** @test */
    public function itShouldAlwaysExecute()
    {
        $cnd = $this->newCondition();
        $cnd->always(function () {
            return true;
        });

        $this->assertTrue($cnd->run());
    }

    /** @test */
    public function itShouldReturnNode()
    {
        $cnd = $this->newCondition();

        $cnd->when(function () {
            return true;
        })->then(function () {
        });

        $this->assertSame($this->node, $cnd->end());
    }

    /** @test */
    public function itShouldNotCloseWhenConditionIsIncomplete()
    {
        $cnd = $this->newCondition();
        $this->node->shouldReceive('getFormattedKey')->andReturn('dummy');

        try {
            $cnd->end();
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Condition of node "dummy" is not declared.', $e->getMessage());
        }

        $cnd = $this->newCondition();
        $cnd->when(function () {
        });

        try {
            $cnd->end();
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Condition of node "dummy" has no result.', $e->getMessage());

            return;
        }

        $this->fail('Test slipped');
    }

    protected function execCallback(Condition $cnd, array $arguments = [])
    {
        try {
            call_user_func_array([$cnd, 'run'], $arguments);
        } catch (\BadMethodCallException $e) {
            $this->assertSame('under test.', $e->getMessage());

            return;
        }

        $this->fail('test slipped.');
    }

    protected function newCondition()
    {
        return new Condition($this->node);
    }

    protected function setUp()
    {
        $this->node = $this->mockNode();
    }

    protected function tearDown()
    {
        m::close();
    }

    protected function mockNode()
    {
        $node = m::mock('Selene\Components\Config\Validator\Nodes\NodeInterface');

        return $node;
    }
}
