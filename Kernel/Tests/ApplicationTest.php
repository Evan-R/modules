<?php

/**
 * This File is part of the Selene\Components\Kernel\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Tests;

use \Mockery as m;
use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Kernel\Application;

/**
 * @class ApplicationTest
 * @package Selene\Components\Kernel\Tests
 * @version $Id$
 */
class ApplicationTest extends TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Kernel\Application', new Application('testing'));
    }

    /** @test */
    public function itShouldRunInConsole()
    {
        $app = new Application('testing');
        $this->assertTrue($app->runsInConsole());
    }

    /** @test */
    public function itShouldDebugg()
    {
        $app = new Application('testing', false);
        $this->assertFalse($app->isDebugging());

        $app = new Application('testing', true);
        $this->assertTrue($app->isDebugging());
    }

    /** @test */
    public function itShouldBoot()
    {
        $app = new Application('testing');

        //$app->boot();
    }

    /**
     * tearDown
     *
     * @return void
     */
    protected function tearDown()
    {
        m::close();
    }
}
