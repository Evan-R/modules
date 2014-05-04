<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use \Selene\Components\DI\Aliases;
use \Selene\Components\TestSuite\TestCase;

/**
 * @class AliasTest
 * @package Selene\Components\DI\Tests
 * @version $Id$
 */
class AliasTest extends TestCase
{
    /**
     * @test
     */
    public function testSetAndGetAliases()
    {
        $alias = new Aliases();
        $alias->set('bar', 'foo');
        $this->assertEquals('foo', $alias->get('bar'));
    }

    /**
     * @test
     */
    public function testGetAndReturnDefault()
    {
        $alias = new Aliases(['bar' => 'foo']);
        $this->assertEquals('foo', $alias->get('foo'));
    }
}
