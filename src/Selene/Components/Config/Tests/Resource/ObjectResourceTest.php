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

use \Selene\Components\Config\Resource\ObjectResource;

/**
 * @class ObjectResourceTest
 * @package Selene\Components\Config\Tests\Resource
 * @version $Id$
 */
class ObjectResourceTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $resource = new ObjectResource($this);

        $this->assertInstanceof('\Selene\Components\Config\Resource\ResourceInterface', $resource);
        $this->assertInstanceof('\Selene\Components\Config\Resource\ObjectResourceInterface', $resource);
    }

    /** @test */
    public function itShouldGetTheRightPath()
    {
        $resource = new ObjectResource($this);
        $this->assertEquals(__FILE__, $resource->getPath());
    }

    /** @test */
    public function itShouldBeValid()
    {
        $time = time() + 10;
        $resource = new ObjectResource($this);
        $this->assertTrue($resource->isValid($time));
    }

    /** @test */
    public function itShouldBeInvalid()
    {
        $mtime = filemtime(__FILE__);
        $time = $mtime - 10;

        $resource = new ObjectResource($this);
        $this->assertFalse($resource->isValid($time));
    }

    /** @test */
    public function itShouldReturnARefelctionObject()
    {
        $resource = new ObjectResource($this);
        $this->assertInstanceof('\ReflectionObject', $resource->getObjectReflection());
    }

    /** @test */
    public function itShouldReturnThisInstance()
    {
        $resource = new ObjectResource($this);
        $this->assertSame($this, $resource->getResource());
    }
}
