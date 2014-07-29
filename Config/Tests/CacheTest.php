<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Cache;

use \Mockery as m;
use \Selene\Components\Config\Cache;
use \Selene\Components\Filesystem\Filesystem;
use \Selene\Components\Config\Resource\FileResource;
use \Selene\Components\TestSuite\TestCase;

/**
 * @class ConfigCacheTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\Config\Tests\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
    protected $fs;

    protected $path;

    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Config\Cache', new Cache('some file'));
    }

    /** @test */
    public function itShouldReportInvalid()
    {
        $cache = new Cache('/foo/file');
        $cache->setDebug(false);

        $this->assertFalse($cache->isValid());
    }

    /** @test */
    public function itShouldReportValid()
    {
        $path = $this->setUpCachefile('ContainerCache.php');

        $cache = new Cache($path);
        $cache->setDebug(false);

        $this->assertTrue($cache->isValid());
    }

    /** @test */
    public function itShouldReportInvalidWhenManifestIsInvalid()
    {
        $path = $this->setUpCachefile('ContainerCache.php');
        $this->setUpManifest(basename($path), ['../some/config.xml']);

        $cache = new Cache($path);
        $cache->setDebug(true);

        $this->assertFalse($cache->isValid());
    }

    /** @test */
    public function itShouldGetFile()
    {
        $path = $this->setUpCachefile('ContainerCache.php');
        $this->setUpManifest(basename($path));

        $cache = new Cache($path);

        $this->assertSame($path, $cache->getFile());
    }

    /** @test */
    public function itShouldValidateManifests()
    {
        $path = $this->setUpCachefile('ContainerCache.php');
        $this->setUpManifest(
            basename($path),
            $files = [$this->mockResource('fileA.xml', true), $this->mockResource('fileB.xml', true)],
            true
        );

        $cache = new Cache($path, true);

        $cache->write('content', $files);

        $this->assertTrue($cache->isValid());

        $files = [$this->mockResource('fileA.xml', false), $this->mockResource('fileB.xml', false)];

        $cache->write('content', $files);

        $this->assertFalse($cache->isValid());
    }

    /** @test */
    public function itShouldForgetCacheFile()
    {
        $path = $this->setUpCachefile('ContainerCache.php');
        $cache = new Cache($path, true);

        $this->assertTrue(is_file($path));
        $cache->forget();
        $this->assertFalse(is_file($path));
    }

    protected function mockResource($path, $valid = false, $exists = true)
    {
        $resource = m::mock('Selene\Components\Config\Resource\FileResource');
        $resource->shouldReceive('isValid')->andReturn($valid);
        $resource->shouldReceive('exists')->andReturn($exists);
        $resource->shouldReceive('__toString')->andReturn($rpath = $this->path . DIRECTORY_SEPARATOR . $path);
        $resource->shouldReceive('getPath')->andReturn($rpath);

        return $resource;
    }

    /**
     * setUpCachefile
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function setUpCachefile($file)
    {
        touch($path = $this->path .'/'. $file);

        return $path;
    }

    /**
     * setUpCachefile
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function setUpManifest($name, array $paths = [], $writePaths = false)
    {
        $file = $this->path .'/'. $name . '.manifest';
        touch($file);

        $p = [];

        foreach ($paths as $path) {
            $p[] = new FileResource($path);
        }

        file_put_contents($file, serialize($p));

        if (!$writePaths) {
            return;
        }

        foreach ($paths as $path) {
            $dir = dirname($path);
            $file = basename($file);

            if (!file_exists($path = $this->path . '/' . $path)) {
                mkdir($path, 0775, true);
            }

            touch($path . '/'. $file);
        }
    }

    protected function setUp()
    {
        $this->fs = new Filesystem;
        $this->path = $this->getTestDir();

        $this->fs->mkdir($this->path, 0775, true);
    }

    protected function getTestDir()
    {
        return dirname(__DIR__).DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'tests';
    }

    protected function teardown()
    {
        $this->fs->remove($this->path);

        m::close();
    }
}
