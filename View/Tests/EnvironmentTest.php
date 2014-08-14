<?php

/**
 * This File is part of the Selene\Module\View\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Tests;

use \Mockery as m;
use \Selene\Module\View\Environment;
use \Selene\Module\View\EngineResolverInterface;
use \Selene\Module\View\TemplateResolverInterface;

/**
 * @class EnvironmentTest
 * @package Selene\Module\View\Tests
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
            m::mock('\Selene\Module\View\EngineResolverInterface'),
            m::mock('\Selene\Module\View\TemplateResolverInterface')
        );

        $this->assertInstanceof('\Selene\Module\View\EnvironmentInterface', $env);
    }

    public function testRender()
    {
        $env = new Environment(
            null,
            $er = m::mock('\Selene\Module\View\EngineResolverInterface'),
            $tr = m::mock('\Selene\Module\View\TemplateResolverInterface')
        );

        $engine = m::mock('\Selene\Module\View\EngineInterface');
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
