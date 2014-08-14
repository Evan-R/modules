<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Resource;

use \Mockery as m;
use \Selene\Module\Config\Loader\Resolver;
use \Selene\Module\Config\Loader\LoaderInterface;
use \Selene\Module\Config\Resource\LoaderResolverInterface;

/**
 * @class LoaderResolverTest
 * @package Selene\Module\Config\Tests\Resource
 * @version $Id$
 */
class ResolverTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $resolver = new Resolver;
        $this->assertInstanceof('\Selene\Module\Config\Loader\ResolverInterface', $resolver);
    }

    /** @test */
    public function itShouldResolverALoaderForAGivenResource()
    {
        $file = 'somefile';

        $resolver = new Resolver;

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
     * @test
     * @expectedException \Selene\Module\Config\Exception\LoaderException
     */
    public function itShouldThrowExceptionWhenNoLoaderIsFound()
    {
        $file = 'somefile';

        $resolver = new Resolver;

        $loaders = [
            $loader = $this->mockLoader($resolver)
        ];

        $loader->shouldReceive('supports')->with($file)->andReturn(false);
        $resolver->setLoaders($loaders);

        $resolver->resolve($file);
    }

    /** @test */
    public function itShouldGetAllLoaders()
    {
        $resolver = new Resolver;

        $loaders = [
            $this->mockLoader($resolver),
            $this->mockLoader($resolver)
        ];

        $resolver->setLoaders($loaders);

        $this->assertSame($loaders, $resolver->all());
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
        $loader  = m::mock('\Selene\Module\Config\Loader\LoaderInterface');
        $loader->shouldReceive('setResolver')->with($resolver);

        return $loader;
    }
}
