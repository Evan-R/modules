<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Resource;

use \org\bovigo\vfs\vfsStream;
use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Config\Resource\Locator;
use \Selene\Module\Config\Resource\LocatorInterface;

/**
 * @class LocatorTest
 * @package Selene\Module\Config\Tests\Resource
 * @version $Id$
 */
class LocatorTest extends TestCase
{
    /**
     * root
     *
     * @var mixed
     */
    protected $root;

    /**
     * path
     *
     * @var string
     */
    protected $path;

    protected function setUp()
    {
        $this->root = vfsStream::setUp('testdrive/resources');
        $this->path = vfsStream::url('testdrive/resources');
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $locator = new Locator;
        $this->assertInstanceof('\Selene\Module\Config\Resource\LocatorInterface', $locator);
    }

    /**
     * @test
     * @dataProvider locationProvider
     */
    public function itShouldLocateFiles(array $locations, $file)
    {
        $locator = new Locator($locations, $this->path);

        $this->assertLocate($locator, $locations, $file);
    }


    /**
     * @test
     * @dataProvider locationProvider
     */
    public function pathsShouldBeSettable(array $locations, $file)
    {
        $locator = new Locator;

        $locator->setRootPath($this->path);
        $locator->setPaths($locations);

        $this->assertLocate($locator, $locations, $file);
    }

    /**
     * @test
     * @dataProvider locationProvider
     */
    public function pathsShouldBeAddable(array $locations, $file)
    {
        $locator = new Locator([], $this->path);

        $locator->addPaths($locations);
        $locator->addPath('invalidpath'); //should be ignored

        $this->assertLocate($locator, $locations, $file);
    }

    /** @test */
    public function itShouldFilterDouplicatePaths()
    {
        $locator = new Locator(['a'], $this->path);

        $locator->addPath('a');
        $locator->addPath('b');

        $paths = $this->getObjectPropertyValue('paths', $locator);

        $this->assertSame(2, count($paths));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExceptionOnInvalidPath()
    {
        $locator = new Locator(['a'], $this->path);

        $locator->setRootPath('root');
    }

    protected function assertLocate($locator, array $locations, $file)
    {
        $paths = $this->setUpLocations($locations, $file);

        $files = $locator->locate($file, true);
        $this->assertSame(count($locations), count($files));

        $resource = $locator->locate($file, false);
        $this->assertSame($paths[0], $resource);
    }

    /**
     * Provides a directory/file map.
     *
     * @return array
     */
    public function locationProvider()
    {
        return [
            [
                ['dirA', 'dirB'], 'examplefile'
            ],
            [
                ['dirC'], 'services.xml'
            ]
        ];
    }

    protected function setUpLocations(array $locations, $file)
    {
        $files = [];

        foreach ($locations as $loc) {
            mkdir($dir = $this->path.DIRECTORY_SEPARATOR.$loc);
            touch($f = $dir.DIRECTORY_SEPARATOR.$file);
            $files[] = $f;
        }

        return $files;
    }
}
