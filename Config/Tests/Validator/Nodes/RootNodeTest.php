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
use \Selene\Module\Config\Tests\Stubs\NodeStub;
use \Selene\Module\Config\Validator\Nodes\RootNode;

class RootNodeTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $node = new RootNode;
        $this->assertInstanceOf('Selene\Module\Config\Validator\Nodes\NodeInterface', $node);
    }

    /** @test */
    public function itShoulThrowExceptionIfSettingAParent()
    {
        $node = m::mock('Selene\Module\Config\Validator\Nodes\NodeInterface');
        $node->shouldReceive('getKey')->andReturn($badName = 'badParentNode');

        $root = new RootNode;
        $root->setKey($key = 'r');

        try {
            $root->setParent($node);
        } catch (\BadMethodCallException $e) {
            $this->assertEquals(
                sprintf('cannot set %s as parent of %s, since %s is root', $badName, $key, $key),
                $e->getMessage()
            );
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('you lose');

    }

    /** @test */
    public function itShouldThrowExceptionIfGetParentIsCalled()
    {
        $root = new RootNode;
        $root->setKey($key = 'r');

        try {
            $root->getParent();
        } catch (\BadMethodCallException $e) {
            $this->assertEquals(
                sprintf('root node %s has no parent', $key),
                $e->getMessage()
            );
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('you lose');
    }
}
