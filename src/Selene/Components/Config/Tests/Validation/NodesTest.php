<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validation package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validation;

use \Mockery as m;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Config\Validation\Nodes;
use \Selene\Components\Config\Validation\ParentInterface;

class NodesTest extends TestCase
{
    /**
     * @test
     */
    public function testNodesConstruct()
    {
        $this->markTestIncomplete();

        $builder = new Nodes();
    }

    /**
     * @dataProvider builderTypeProvider
     */
    public function testAddNode($type)
    {
        $this->markTestIncomplete();

        $parent = m::mock('Selene\Components\Config\Validation\ParentInterface');
        $parent->shouldReceive('getPath')->andReturn('root');
        $builder = new Nodes($parent);

        $builder->addNode('child', $type);
    }

    public function builderTypeProvider()
    {
        return [
            ['scalar'],
            ['string'],
            ['boolean'],
            ['array'],
            ['list']
        ];
    }

    /**
     * @test
     */
    public function testAddInvalidNode()
    {
        $builder = new Nodes();
        try {
            $builder->addNode('foo', 'scrambled');
        } catch (\InvalidArgumentException $e) {
            $this->assertEquals('unknown type "scrambled"', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail(
                sprintf(
                    '->addNode(): setting an invalid node should throw an InvalidArgumentException,
                    instead saw exception with message: %s',
                    $e->getMessage()
                )
            );
        }
    }
}
