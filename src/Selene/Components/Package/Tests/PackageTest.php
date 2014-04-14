<?php

/**
 * This File is part of the Selene\Components\Package\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package\Tests;

use \Mockery as m;
use \Selene\Components\Package\Package;
use \Selene\Components\Core\Application;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Package\Tests\Stubs\StubPackage;

/**
 * @class PackageTest
 * @package Selene\Components\Package\Tests
 * @version $Id$
 */
class PackageTest extends TestCase
{
    /**
     * @test
     */
    public function testGetName()
    {
        $package = new StubPackage(m::mock('Selene\Components\Core\Application'));
        $this->assertSame('StubPackage', $package->getName());
    }

    /**
     * @test
     */
    public function testGetNamespace()
    {
        $package = new StubPackage(m::mock('Selene\Components\Core\Application'));
        $this->assertSame(__NAMESPACE__.'\\Stubs', $package->getNamespace());
    }

    /**
     * @test
     */
    public function testGetPath()
    {
        $package = new StubPackage(m::mock('Selene\Components\Core\Application'));
        $this->assertSame(__DIR__.DIRECTORY_SEPARATOR.'Stubs', $package->getPath());
    }
}
