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

use \Mockery as m;
use \Selene\Components\Routing\Route;
use \Selene\Components\Routing\RouteCompiler as Compiler;

/**
 * @class RouteCompilerTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class RouteCompilerTest extends \PHPUnit_Framework_TestCase
{

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldReturnTheStaticPath()
    {
        $route = new Route('foo.bar', '/{bar?}', 'GET');

        $args = Compiler::compile($route);

        $this->assertEquals('/', $args['static_path']);

        $route = new Route('foo.bar', '/foo/bar', 'GET');

        $args = Compiler::compile($route);

        $this->assertEquals('/foo/bar', $args['static_path']);
    }

    /**
     * @test
     */
    public function routesSouldBeAddableBam()
    {
        $route = new Route('foo.bar', '/foo/{bar}/bar/{baz}', 'GET');

        $args = Compiler::compile($route);

        $this->assertEquals('/foo', $args['static_path']);
    }

    /**
     * @test
     */
    public function routesSouldBeAddable()
    {
        $route = new Route('foo', '/bam/{bar}/{baz?}', 'GET', [
            '_host' => '{domain}.domain.com',
            '_constraints' => ['bar' => '(\d+)']

            ]);

        $args = Compiler::compile($route);
        $this->assertEquals('/bam', $args['static_path']);
    }

    /**
     * @test
     */
    public function numericVarsShouldThrowException()
    {
        $route = new Route('foo', '/bam/{1}/{2}', 'GET');

        try {
            $args = Compiler::compile($route);
        } catch (\DomainException $e) {
            $this->assertSame('1 is not a valid parameter', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->fail('you loose');
    }

    /**
     * @test
     */
    public function doublicateVarsShouldThrowException()
    {
        $route = new Route('foo', '/bam/{bar}/{bar}', 'GET');

        try {
            $args = Compiler::compile($route);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('variable {bar} in a uri must not be repeated', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->fail('you loose');
    }

    /**
     * @test
     */
    public function optionalHostVarsShouldThrowException()
    {
        $route = new Route('foo', '/bam/{bar}/{baz?}', 'GET', [
            '_host' => '{domain?}.domain.com',
        ]);

        try {
            $args = Compiler::compile($route);
        } catch (\DomainException $e) {
            $this->assertSame('domain host expression may not contain optional placeholders', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
        $this->fail('you loose');
    }

    /**
     * @test
     * @dataProvider patternResolverProvider
     */
    public function itShouldCompileToValidRegexp($pattern, $regexp, $path, $host = false, $requirement = null)
    {
        $route = new Route('index', $pattern, 'GET');

        if ($host) {
            $route->setHost($host['pattern']);
        }

        if (null !== $requirement) {
            list ($param, $req) = $requirement;
            if ($host) {
                $route->setHostConstraint($param, $req);
            } else {
                $route->setParamConstraint($param, $req);
            }
        }

        $args = Compiler::compile($route);

        if ($host) {
            $this->assertEquals($regexp, $args['host']['regexp']);
        } else {
            $this->assertEquals($regexp, $args['regexp']);

            foreach ((array)$path as $p) {
                $this->assertTrue((bool)preg_match($args['regexp'], $p), sprintf('regexp %s should match %s', $args['regexp'], $p));
            }
        }


        foreach ((array)$path as $p) {
            $this->assertRegexp($regexp, $p);
        }
    }

    public function patternResolverProvider()
    {
        return [
            [
                '/foo/bar',
                '#^/foo/bar$#s',
                '/foo/bar',
                false
            ],
            [
                '/{opt1?}',
                '#^/(?P<opt1>[^/]++)?$#s',
                ['/', '/opt'],
                false
            ],
            [
                '/foo/{opt}/bar/{opt2?}',
                '#^/foo/(?P<opt>[^/]++)/bar(?:/(?P<opt2>[^/]++))?$#s',
                ['/foo/opt/bar','/foo/opt/bar/opt2'],
                false
            ],
            [
                '/foo/fii/{opt1?}',
                '#^/foo/fii(?:/(?P<opt1>[^/]++))?$#s',
                ['/foo/fii', '/foo/fii/bar'],
                false
            ],
            [
                '/foo/bam/{opt1}/{opt2?}',
                '#^/foo/bam/(?P<opt1>[^/]++)(?:/(?P<opt2>[^/]++))?$#s',
                ['/foo/bam/boo', '/foo/bam/boo/baz'],
                false
            ],
            [
                '/foo/bam/{opt1}/{opt2}/{opt3?}',
                '#^/foo/bam/(?P<opt1>[^/]++)/(?P<opt2>[^/]++)(?:/(?P<opt3>[^/]++))?$#s',
                '/foo/bam/a/b/c',
                false
            ],
            [
                '/foo/bam/{opt1?}/{opt2?}/{opt3?}',
                '#^/foo/bam(?:/(?P<opt1>[^/]++)(?:/(?P<opt2>[^/]++)(?:/(?P<opt3>[^/]++))?)?)?$#s',
                '/foo/bam/a/b',
                false
            ],
            [
                '/foo/bam/{opt1?}{opt2?}{opt3?}',
                '#^/foo/bam(?:/(?P<opt1>[^/]+)(?:(?P<opt2>[^/]+)(?:(?P<opt3>[^/]++))?)?)?$#s',
                '/foo/bam/abc',
                false
            ],
            [
                '/foo/bar/{opt1?}/{opt2?}',
                '#^/foo/bar(?:/(?P<opt1>[^/]++)(?:/(?P<opt2>[^/]++))?)?$#s',
                '/foo/bar/a',
                false
            ],
            [
                '/foo/bar/{opt1?}.{opt2?}',
                '#^/foo/bar(?:/(?P<opt1>[^/\.]++)(?:\.(?P<opt2>(\d+)))?)?$#s',
                '/foo/bar/a.2',
                false,
                ['opt2', '(\d+)']
            ],
            [
                '/foo/bar/{opt1}',
                '#^/foo/bar/(?P<opt1>(.*))$#s',
                '/foo/bar/match/all/the/things',
                false,
                ['opt1', '(.*)']
            ],
            [
                '/foo/bar/{opt1?}/{opt2?}',
                '#^(?P<domain>mobile|m)\.example\.com$#s',
                ['m.example.com', 'mobile.example.com'],
                [
                    'pattern' => '{domain}.example.com'
                ],
                ['domain', 'mobile|m']
            ]
        ];
    }
}
