<?php

/**
 * This File is part of the Selene\Components\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests;

use \Selene\Components\Routing\Route;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function itSouldBeInstatiable()
    {
        $route = new Route('foo', 'foo');
        $this->assertInstanceOf('Selene\Components\Routing\Route', $route);
    }

    /**
     * @test
     */
    public function itSouldComplainIfNoActionIsSet()
    {
        $route = new Route('foo', '/foo/{bar}/{bam?}', 'GET');

        try {
            $route->compile();
        } catch (\BadMethodCallException $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * @test
     */
    public function itSouldCompile()
    {
        $route = new Route('foo', '/foo/{bar}/{bam?}', 'GET', ['_action' => 'foo']);
        $route->compile();

        $this->assertTrue($route->isCompiled());
    }

    /**
     * @test
     */
    public function itSouldBeCompiledAfterDeserialasation()
    {
        $route = new Route('foo', '/foo/{bar}/{bam?}', 'GET', ['_action' => 'foo']);

        $str = serialize($route);
        $route = unserialize($str);

        $this->assertTrue($route->isCompiled());
    }

    /**
     * @test
     */
    public function itShouldPresentAStaticPathAfterCompilation()
    {
        $route = new Route('foo', '/foo/{bar}/{bam?}', 'GET', ['_host' => '{sub}.domain.{tld}', '_action' => 'foo']);
        $route->compile();
        $this->assertSame('/foo', $route->getStaticPath());
    }

    /**
     * @test
     */
    public function itShouldNotBeSecure()
    {
        $route = new Route('index', '/');
        $this->assertFalse($route->isSecure());
    }

    /**
     * @test
     */
    public function itShouldBeSecure()
    {
        $route = new Route('index', '/', 'GET', ['_schemes' => ['https']]);
        $this->assertTrue($route->isSecure());
    }

    /**
     * @test
     */
    public function itShouldHaveParameterConstraints()
    {
        $route = new Route('index', '/{foo}', 'GET', ['_constraints' => ['route' => ['foo' => '(\d+)']]]);

        $this->assertTrue((bool)$route->getParamConstraint('foo'));
        $this->assertSame('(\d+)', $route->getParamConstraint('foo'));


        $route = new Route('index', '/{foo}', 'GET');

        $route->setParamConstraint('foo', '(\d+)');

        $this->assertTrue((bool)$route->getParamConstraint('foo'));
        $this->assertSame('(\d+)', $route->getParamConstraint('foo'));

        $route = new Route('index', '/{foo}', 'GET');

        $route->setConstraint('foo', '(\d+)');

        $this->assertTrue((bool)$route->getConstraint('foo'));
        $this->assertSame('(\d+)', $route->getConstraint('foo'));

    }
    /**
     * @test
     */
    public function itShouldHaveHostConstraints()
    {
        $route = new Route('index', '/{foo}', 'GET', ['_constraints' => ['host' => ['foo' => '(\d+)']]]);

        $this->assertTrue((bool)$route->getHostConstraint('foo'));
        $this->assertSame('(\d+)', $route->getHostConstraint(('foo')));

        $route = new Route('index', '/{foo}', 'GET');

        $route->setHostConstraint('foo', '(\d+)');

        $this->assertTrue((bool)$route->getHostConstraint('foo'));
        $this->assertSame('(\d+)', $route->getHostConstraint('foo'));
    }
}
