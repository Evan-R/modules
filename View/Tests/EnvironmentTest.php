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
use \Selene\Components\View\Environment;
use \Selene\Components\View\EngineResolverInterface;
use \Selene\Components\View\TemplateResolverInterface;

/**
 * @class EnvironmentTest
 * @package Selene\Components\View\Tests
 * @version $Id$
 */
class EnvironmentTest extends \PHPUnit_Framework_TestCase
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
        $env = new Environment(
            null,
            m::mock('\Selene\Components\View\EngineResolverInterface'),
            m::mock('\Selene\Components\View\TemplateResolverInterface')
        );

        $this->assertInstanceof('\Selene\Components\View\EnvironmentInterface', $env);
    }

    public function testRender()
    {
        $env = new Environment(
            null,
            $er = m::mock('\Selene\Components\View\EngineResolverInterface'),
            $tr = m::mock('\Selene\Components\View\TemplateResolverInterface')
        );

        $engine = m::mock('\Selene\Components\View\EngineInterface');
        $engine->shouldReceive('render');
        $er->shouldReceive('addEngine')->with($engine);
        $er->shouldReceive('resolve')->with('twig')->andReturn($engine);

        $tr->shouldReceive('resolve')->with('template')->andReturn([
            $info = m::mock('SplFileInfo')
        ]);

        $info->shouldReceive('getExtension')->andReturn('twig');
        $info->shouldReceive('getPathInfo')->andReturn('/somepath/template.twig');

        $env->registerEngine($engine);

        $env->render('template', ['foo' => 'bar']);
    }
}
