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
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Config\Resource\FileResource;
use \Selene\Components\Filesystem\Filesystem;

/**
 * @class FileResourceTest
 * @package Selene\Components\Config\Tests\Resource
 * @version $Id$
 */
class FileResourceTest extends TestCase
{

    protected static $fsCompare = 'isFile';

    protected function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    /**
     * @test
     * @dataProvider validationProvider
     */
    public function testIsValid($file, $isFile, $retval = true, $filemtime = null, $timestamp = null)
    {
        $fs = m::mock('Selene\Components\Filesystem\Filesystem');
        $fs->shouldReceive(static::$fsCompare)->with($file)->andReturn($isFile);

        if ($isFile) {
            $fs->shouldReceive('fileMTime')->andReturn($filemtime);
        } else {
            $fs->shouldReceive('fileMTime')->andReturnUsing(function () {
                $this->fail('->isValid() timestamp comparision should not occurre if resource is not a file');
            });
        }

        $resource = $this->getResource($file, $fs);

        $this->assertSame($retval, $resource->isValid($timestamp));
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $resource = $this->getResource($file = '/some/fime');
        $this->assertEquals($file, $resource->getPath());
    }

    /**
     * validationProvider
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            ['/some/file', false, false],
            ['/some/file', true, false, 1, 0],
            ['/some/file', true, true, 0, 1]
        ];
    }

    protected function getResource($file, $fs = null)
    {
        return new FileResource($file, $fs);
    }
}
