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

use \org\bovigo\vfs\vfsStream;
use \Selene\Components\Config\Cache;
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
class CacheTest extends TestCase
{
    protected $root;
    protected $path;
    protected function setUp()
    {
        $this->root = vfsStream::setUp('testdrive/cache');
        $this->path = vfsStream::url('testdrive/cache');

        var_dump($this->path);
    }

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
}
