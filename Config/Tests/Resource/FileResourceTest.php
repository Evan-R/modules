<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Resource;

use \Mockery as m;
use \org\bovigo\vfs\vfsStream;
use \Selene\Components\Config\Resource\FileResource;

/**
 * @class FileResourceTest
 * @package Selene\Components\Config\Tests\Resource
 * @version $Id$
 */
class FileResourceTest extends \PHPUnit_Framework_TestCase
{
    protected $root;

    protected $path;

    protected function setUp()
    {
        $this->root = vfsStream::setUp('testdrive/resources');
        $this->path = vfsStream::url('testdrive/resources');
    }

    protected function tearDown()
    {
        m::close();
    }

    /** @test */
    public function itShouldBeInstantiable()
    {
        $resource = new FileResource('somefile');
        $this->assertInstanceof('\Selene\Components\Config\Resource\FileResource', $resource);
    }

    /**
     * @test
     * @dataProvider validationProvider
     */
    public function itShouldReportIfItIsValid($file, $isFile, $retval = true, $filemtime = null, $timestamp = null)
    {
        $file = $this->createResourceFile($file, $filemtime, $isFile);

        $resource = $this->getResource($file);

        $this->assertSame($retval, $resource->isValid($timestamp));
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $resource = $this->getResource($file = '/some/fime');
        $this->assertEquals($file, (string)$resource);
    }

    /**
     * validationProvider
     *
     * @return array
     */
    public function validationProvider()
    {
        $time = time();

        $providers = [
            ['fileAA', false, false],
            ['fileBB', true, false, $time, $time - 1000],
            ['fileCC', true, true, $time - 1000, $time]
        ];

        return $providers;
    }

    protected function createResourceFile($file, $timestamp, $isFile = true)
    {
        $file = $this->path.DIRECTORY_SEPARATOR.$file;

        if (!$isFile) {
            return $file;
        }

        touch($file, $timestamp);

        return $file;
    }

    protected function getResource($file)
    {
        return new FileResource($file);
    }
}
