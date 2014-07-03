<?php

/**
 * This File is part of the Selene\Components\Kernel\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Tests;

use \Mockery as m;
use \org\bovigo\vfs\vfsStream;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Kernel\Application;
use \Selene\Components\Kernel\Tests\Stubs\ApplicationStub;

/**
 * @class ApplicationTest
 * @package Selene\Components\Kernel\Tests
 * @version $Id$
 */
class ApplicationTest extends TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Kernel\Application', new Application('testing'));
    }

    /** @test */
    public function itShouldRunInConsole()
    {
        $app = new Application('testing');
        $this->assertTrue($app->runsInConsole());
    }

    /** @test */
    public function itShouldDebugg()
    {
        $app = new Application('testing', false);
        $this->assertFalse($app->isDebugging());

        $app = new Application('testing', true);
        $this->assertTrue($app->isDebugging());
    }

    /** @test */
    public function itShouldBoot()
    {
        $app = $this->prepareApp('testing');

        $this->replaceContainerSubCode();

        $app->mockCache = $this->mockCache(true);

        $app->boot();
    }

    protected function mockCache($valid = false, $env = 'testing')
    {
        $cache = m::mock('Selene\Components\Config\CacheInterface');
        $cache->shouldReceive('isValid')->andReturn((bool)$valid);

        $cache->shouldReceive('getFile')->andReturn($this->replaceContainerSubCode($env));

        return $cache;
    }

    protected function prepareApp($name = 'testing', $debug = false)
    {
        $app = new ApplicationStub($name, $debug);

        //$this->container = $app->getMockObject();

        $this->setAppPaths($app);

        return $app;
    }

    protected function setAppPaths($app)
    {
        $app->setApplicationRoot($this->rootPath);
        $app->setContainerCachePath($this->cachePath);
    }

    protected function setUp()
    {
        $this->root = vfsStream::setUp('root');
        $this->rootPath = vfsStream::url('root');

        mkdir($this->cachePath = $this->rootPath . '/cache');
    }

    protected function replaceContainerSubCode($env = 'testing')
    {
        $file = __DIR__.'/Stubs/ContainerMock';

        $source = file_get_contents($file);

        $suffix = ucfirst($env);
        $ns = 'Selene\\ClassCache';
        $class = 'Container' . $suffix;

        $newSource = strtr($source, ['%namespace%' => $ns, '%class%' => $class]);

        //echo $newSource;
        //die;

        file_put_contents(
            $file = $this->cachePath.'/Container'.$suffix . '.php',
            $newSource
        );

        return $file;
    }

    /**
     * tearDown
     *
     * @return void
     */
    protected function tearDown()
    {
        m::close();
    }
}
