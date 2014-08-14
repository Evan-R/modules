<?php

/**
 * This File is part of the Selene\Module\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests;

use \Selene\Module\DI\Aliases;
use \Selene\Module\TestSuite\TestCase;

/**
 * @class AliasTest
 * @package Selene\Module\DI\Tests
 * @version $Id$
 */
class AliasesTest extends TestCase
{
    /**
     * @test
     */
    public function testSetAndGetAliases()
    {
        $alias = new Aliases();
        $alias->set('bar', 'foo');
        $this->assertEquals('foo', (string)$alias->get('bar'));
    }

    /**
     * @test
     */
    public function testGetAndReturnDefault()
    {
        $alias = new Aliases(['bar' => 'foo']);

        $this->assertEquals('foo', $alias->get('foo'));
        $this->assertEquals('foo', $alias->get('bar'));
    }

    /** @test */
    public function itShouldGetAllAliases()
    {
        $aliases = new Aliases($props = ['bar' => 'foo']);

        $this->assertSame(1, count($aliases->all()));
    }

    /** @test */
    public function itShouldOfferArrayAccess()
    {
        $aliases = new Aliases;

        $aliases['foo'] = 'bar';

        $this->assertTrue(isset($aliases['foo']));
        $this->assertSame('bar', (string)$aliases['foo']);

        unset($aliases['foo']);

        $this->assertFalse(isset($aliases['foo']));
    }

    /** @test */
    public function itShouldBeItertable()
    {
        $data = ['foo' => 'foo.service', 'bar' => 'bar.service'];
        $aliases = new Aliases($data);

        $result = [];

        foreach ($aliases as $alias => $id) {
            $result[$alias] = (string)$id;
        }

        $this->assertSame($data, $result);
    }
}
