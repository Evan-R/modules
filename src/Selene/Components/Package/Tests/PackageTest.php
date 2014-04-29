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

    /** @test */
    public function itShouldGetItsName()
    {
        $package = new StubPackage;
        $this->assertSame('StubPackage', $package->getName());
    }

    /** @test */
    public function itShouldGetItsAlias()
    {
        $package = new StubPackage;
        $this->assertSame('stub', $package->getAlias());
    }

    /** @test */
    public function itShouldGetItsNamespace()
    {
        $package = new StubPackage;
        $this->assertSame(__NAMESPACE__.'\\Stubs', $package->getNamespace());
    }

    /** @test */
    public function itShouldGetItsFilePath()
    {
        $package = new StubPackage;
        $this->assertSame(__DIR__.DIRECTORY_SEPARATOR.'Stubs', $package->getPath());
    }

    /** @test */
    public function itShouldGetItsMetaInfo()
    {
        $package = new StubPackage;
        $this->assertSame(__DIR__.DIRECTORY_SEPARATOR.'Stubs'.DIRECTORY_SEPARATOR.'meta.xml', $package->getMeta());
    }
}
