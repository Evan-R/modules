<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests;

use \Mockery as m;
use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\UrlBuilder;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class UrlBuilderTest
 * @package Selene\Components\Routing
 * @version $Id$
 */
class UrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof(
            'Selene\Components\Routing\UrlBuilder',
            new UrlBuilder($this->getRoutes(), $this->getStack())
        );
    }

    /** @test */
    public function itShouldGetTheCurrentUrl()
    {
        $req = Request::create($path = '/foo/bar');

        $builder = new UrlBuilder($this->getRoutes(), $stack = $this->getStack());
        $stack->shouldReceive('getCurrent')->andReturn($req);

        $this->assertSame($path, $builder->currentUrl());

        $this->assertSame('http://localhost'.$path, $builder->currentUrl(UrlBuilder::ABSOLUTE_PATH));

        $req = Request::create($path = '/foo/bar?foo=bar');

        $builder = new UrlBuilder($this->getRoutes(), $stack = $this->getStack());
        $stack->shouldReceive('getCurrent')->andReturn($req);

        $this->assertSame($path, $builder->currentUrl());

        $this->assertSame('http://localhost'.$path, $builder->currentUrl(UrlBuilder::ABSOLUTE_PATH));
    }

    /** @test */
    public function itShouldGetTheCurrentPath()
    {
        $req = Request::create('/foo/bar?foo=bar');

        $builder = new UrlBuilder($this->getRoutes(), $stack = $this->getStack());
        $stack->shouldReceive('getCurrent')->andReturn($req);

        $this->assertSame('/foo/bar', $builder->currentPath());

        $this->assertSame('http://localhost/foo/bar', $builder->currentPath(UrlBuilder::ABSOLUTE_PATH));
    }

    /** @test */
    public function itShouldGenerateUrls()
    {
        $req = Request::create('/foo/bar');

        $builder = new UrlBuilder($routes = $this->getRoutes(), $stack = $this->getStack());

        $routes->shouldReceive('get')
            ->with('index')
            ->andReturn(
                $route = new Route('index', '/', 'GET', ['_action' => 'foo'])
            );

        $stack->shouldReceive('getCurrent')->andReturn($req);

        $this->assertSame('', $builder->getPath('index'));
        $this->assertSame('http://localhost', $builder->getPath('index', [], null, UrlBuilder::ABSOLUTE_PATH));
    }

    /** @test */
    public function itShouldGenerateUrlsAndDoStuff()
    {
        $req = Request::create('/foo/bar');

        $builder = new UrlBuilder($routes = $this->getRoutes(), $stack = $this->getStack());

        $routes->shouldReceive('get')->with('index')->andReturn(
            $route = new Route('index', '/foo/{id}', 'GET', ['_action' => 'foo'])
        );

        $stack->shouldReceive('getCurrent')->andReturn($req);

        $this->assertSame('/foo/12', $builder->getPath('index', ['id' => 12]));

        $builder = new UrlBuilder($routes = $this->getRoutes(), $stack);

        $routes->shouldReceive('get')->with('index')->andReturn(
            $route = new Route('index', '/foo/{id}/{name?}/{surname?}', 'GET', ['_action' => 'foo'])
        );

        $stack->shouldReceive('getCurrent')->andReturn($req);

        $this->assertSame('/foo/12', $builder->getPath('index', ['id' => 12]));
        $this->assertSame('/foo/12/mark', $builder->getPath('index', ['id' => 12, 'name' => 'mark']));

        try {
            $builder->getPath('index', ['id' => 12, 'surname' => 'smith']);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Variable "name" must not be empty.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldCareAboutTheHost()
    {
        $req = Request::create('/');

        $builder = new UrlBuilder($routes = $this->getRoutes(), $stack = $this->getStack());

        $routes->shouldReceive('get')->with('index')->andReturn(
            $route = new Route('index', '/', 'GET', ['_action' => 'foo'])
        );

        $route->setHost('selene.{tld}')->setHostConstraint('tld', '(dev|com)');

        $stack->shouldReceive('getCurrent')->andReturn($req);

        try {
            $builder->getPath('index');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'Can\'t create relative path because route requires a deticated hostname',
                $e->getMessage()
            );
        }

        try {
            $builder->getPath('index', [], 'selene.de', UrlBuilder::RELATIVE_PATH);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'Can\'t create relative path because route requires a deticated hostname',
                $e->getMessage()
            );
        }

        try {
            $builder->getPath('index', [], 'selene.de', UrlBuilder::ABSOLUTE_PATH);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Host requirement does not match given host.', $e->getMessage());
        }

        $this->assertSame('http://selene.dev', $builder->getPath('index', [], 'selene.dev', UrlBuilder::ABSOLUTE_PATH));
        $this->assertSame('http://selene.com', $builder->getPath('index', [], 'selene.com', UrlBuilder::ABSOLUTE_PATH));
    }

    protected function tearDown()
    {
        m::close();
    }

    protected function getStack()
    {
        return m::mock('Selene\Components\Http\RequestStack, Selene\Components\Http\StackInterface');
    }

    protected function getRoutes()
    {
        return m::mock('Selene\Components\Routing\RouteCollectionInterface');
    }
}
