<?php

/**
 * This File is part of the Selene\Module\View\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Tests\Template;

use \Mockery as m;
use \Selene\Module\View\Template\TemplateResolver;
use \Selene\Module\View\Template\EngineResolver;
use \Selene\Module\View\Template\EngineResolverInterface;
use \Selene\Module\View\Template\TemplateResolverInterface;

/**
 * @class EngineResolverTest
 * @package Selene\Module\View\Tests
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
        $this->assertInstanceof('Selene\Module\View\Template\EngineResolverInterface', $resolver);
    }

    /**
     * @test
     */
    public function itShouldResolveExtension()
    {
        $engineTwig = $this->mockEngine('twig');
        $enginePhp  = $this->mockEngine('php');

        $resolver = new EngineResolver([$engineTwig, $enginePhp]);

        $this->assertSame($engineTwig, $resolver->resolve('twig'));
        $this->assertSame($enginePhp, $resolver->resolve('php'));
    }

    /** @test */
    public function itShouldHandleAliases()
    {
        $engine = $this->mockEngine('twig');

        $resolver = new EngineResolver;
        $resolver->registerEngine($engine);

        $resolver->setAlias('twig', 'html');

        $this->assertSame('twig', $resolver->getAlias('html'));
        $this->assertSame('foo', $resolver->getAlias('foo'));
        $this->assertSame($engine, $resolver->resolve('html'));
    }

    /** @test */
    public function itShouldResolveCallbacks()
    {
        $engine = $this->mockEngine('twig');

        $resolver = new EngineResolver;
        $resolver->register('twig', function () use ($engine) {
            return $engine;
        });

        $this->assertSame($engine, $resolver->resolve('twig'));
    }

    /** @test */
    public function itShouldResolveEngineByName()
    {
        $engine = $this->mockEngine('twig');

        $resolver = new EngineResolver;
        $resolver->register('twig', function () use ($engine) {
            return $engine;
        });

        $this->assertSame($engine, $resolver->resolveByName('template.twig'));
    }

    protected function mockEngine($type)
    {
        $engine = m::mock('\Selene\Module\View\Template\EngineInterface');

        $engine->shouldReceive('supports')->with($type)->andReturn(false);
        $engine->shouldReceive('supports')->with(m::any())->andReturn(true);
        $engine->shouldReceive('getType')->andReturn($type);

        return $engine;
    }
}
