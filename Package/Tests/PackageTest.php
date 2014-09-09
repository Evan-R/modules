<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Tests;

use \Mockery as m;
use \Selene\Module\Package\Package;
use \Selene\Module\Core\Application;
use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Package\Tests\Stubs\StubPackage;

/**
 * @class PackageTest
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Module\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class PackageTest extends \PHPUnit_Framework_TestCase
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
}
