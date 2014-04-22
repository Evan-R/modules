<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Resource;

use \Mockery as m;
use \Selene\Components\Config\Resource\LoaderResolver;
use \Selene\Components\Config\Resource\LoaderInterface;
use \Selene\Components\Config\Resource\LoaderResolverInterface;

/**
 * @class LoaderResolverTest
 * @package Selene\Components\Config\Tests\Resource
 * @version $Id$
 */
class LoaderResolverTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $resolver = new LoaderResolver;
        $this->assertInstanceof('\Selene\Components\Config\Resource\LoaderResolverInterface', $resolver);
    }

    /** @test */
    public function itShouldResolverALoaderForAGivenResource()
    {
        $file = 'somefile';

        $resolver = new LoaderResolver;

        $loaders = [
            $a = $this->mockLoader($resolver),
            $b = $this->mockLoader($resolver),
            $c = $this->mockLoader($resolver)
        ];

        $a->shouldReceive('supports')->with($file)->andReturn(false);
        $b->shouldReceive('supports')->with($file)->andReturn(true);
        $c->shouldReceive('supports')->with($file)->andReturn(false);

        $resolver->setLoaders($loaders);

        $this->assertSame($b, $resolver->resolve($file));

        $file = 'someotherfile';

        $a->shouldReceive('supports')->with($file)->andReturn(true);
        $b->shouldReceive('supports')->with($file)->andReturn(false);
        $c->shouldReceive('supports')->with($file)->andReturn(false);

        $this->assertSame($a, $resolver->resolve($file));
    }

    /**
     * mockLoader
     *
     * @param mixed $resolver
     *
     * @access protected
     * @return LoaderInterface
     */
    protected function mockLoader($resolver)
    {
        $loader  = m::mock('\Selene\Components\Config\Resource\LoaderInterface');
        $loader->shouldReceive('setResolver')->with($resolver);

        return $loader;
    }
}
