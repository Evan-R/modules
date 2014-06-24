<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Resource;

use \org\bovigo\vfs\vfsStream;
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\Config\Resource\LocatorInterface;

/**
 * @class LocatorTest
 * @package Selene\Components\Config\Tests\Resource
 * @version $Id$
 */
class LocatorTest extends \PHPUnit_Framework_TestCase
{
    protected $root;

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
        $this->assertInstanceof('\Selene\Components\Config\Resource\LocatorInterface', $locator);
    }

    /**
     * @test
     * @dataProvider locationProvider
     */
    public function itShouldLocateFiles(array $locations, $file)
    {
        $paths = $this->setUpLocations($locations, $file);

        $locator = new Locator($locations, $this->path);

        $files = $locator->locate($file, true);

        $this->assertSame(count($locations), count($files));

        $resource = $locator->locate($file, false);

        $this->assertSame($paths[0], $resource);
    }

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
