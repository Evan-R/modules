<?php

/**
 * This File is part of the Selene\Components\View\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Tests;

use \Mockery as m;
use \Selene\Components\View\TemplateResolver;
use \Selene\Components\View\EngineResolver;
use \Selene\Components\View\EngineResolverInterface;
use \Selene\Components\View\TemplateResolverInterface;

/**
 * @class EngineResolverTest
 * @package Selene\Components\View\Tests
 * @version $Id$
 */
class EngineResolverTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $resolver = new EngineResolver;
        $this->assertInstanceof('Selene\Components\View\EngineResolverInterface', $resolver);
    }

    /**
     * @test
     */
    public function itShouldResolveExtension()
    {
        $engineTwig = m::mock('\Selene\Components\View\EngineInterface');
        $enginePhp  = m::mock('\Selene\Components\View\EngineInterface');

        $engineTwig->shouldReceive('supports')->with('twig')->andReturn(true);
        $engineTwig->shouldReceive('supports')->with('php')->andReturn(false);

        $enginePhp->shouldReceive('supports')->with('twig')->andReturn(false);
        $enginePhp->shouldReceive('supports')->with('php')->andReturn(true);

        $resolver = new EngineResolver([$engineTwig, $enginePhp]);

        $this->assertSame($engineTwig, $resolver->resolve('twig'));
        $this->assertSame($enginePhp, $resolver->resolve('php'));
    }
}
