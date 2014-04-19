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

use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Config\Validation\Tree;
use \Selene\Components\Config\Validation\Builder;

/**
 * @class BuilderTest
 * @package Selene\Components\Config\Tests\Validation
 * @version $Id$
 */
class BuilderTest extends TestCase
{

    /**
     * @test
     */
    public function testEndNodes()
    {
        $builder = new Builder;
        $root = $builder->root('root');

        $this->assertEquals($builder, $root->end());

        $builder = new Builder;
        $root = $builder->root('root');

        $child = $root->arrayNode('bar');

        $this->assertEquals($root, $child->end());

        $builder = new Builder;

        $root = $builder->root('root');

        $child = $root->arrayNode('bar');
        $childChild = $child->arrayNode('fuzz');

        $this->assertEquals($child, $childChild->end());
        $this->assertEquals($root, $child->end());


        $builder = new Builder;
        $root = $builder->root('root');
        $root->scalarNode('foo')
            ->end();
        $array = $root->arrayNode('bar');
        $array
            ->scalarNode('fuzz')
            ->end()
            ->scalarNode('fud')
            ->end()
            ->scalarNode('fub')
            ->end();

        $this->assertEquals($root, $array->end());
        $this->assertEquals($builder, $root->end());
    }

    /**
     * @test
     */
    public function testBuilderGetRootKeys()
    {
        $builder = new Builder;

        $root = $builder->root('root');
        $root->scalarNode('foo')
            ->end();
        $array = $root->arrayNode('bar');
        $foo = $array
            ->scalarNode('fuzz')
            ->end()
            ->scalarNode('fud')
            ->end()
            ->scalarNode('fub')
            ->end()
        ->end()
        ->scalarNode('baz')->end()
        ->end();

        $this->assertEquals(['foo', 'bar', 'baz'], $root->keys());
        $this->assertEquals(['fuzz', 'fud', 'fub'], $array->keys());
    }
}
