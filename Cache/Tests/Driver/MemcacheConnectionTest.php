<?php

/**
 * This File is part of the Selene\Components\Cache\Tests\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Tests\Driver;

use \Selene\Components\Cache\Driver\MemcacheConnection;

/**
 * @class MemcacheConnectionTest
 * @package Selene\Components\Cache\Tests\Driver
 * @version $Id$
 */
class MemcacheConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Cache\Driver\ConnectionInterface', new MemcacheConnection);
    }

    /** @test */
    public function itShouldConnectToServer()
    {
        $conn = new MemcacheConnection(new \Memcache, [['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]]);

        $this->assertFalse($conn->isConnected());

        $conn->connect();

        $this->assertTrue($conn->isConnected());

    }

    /** @test */
    public function itShouldCloseConnections()
    {
        $conn = new MemcacheConnection(new \Memcache, [['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]]);

        $conn->connect();
        $this->assertTrue($conn->isConnected());

        $conn->close();
        $this->assertFalse($conn->isConnected());
    }

    /** @test */
    public function itShouldReturnFalseIfAlreadyConnection()
    {
        $conn = new MemcacheConnection(new \Memcache, [['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]]);
        $this->assertTrue($conn->connect());
        $this->assertFalse($conn->connect());
    }

    /** @test */
    public function itShouldThrowExceptionOnConnectionFailure()
    {
        $conn = new MemcacheConnection(new \Memcache, [['host' => 'fakehost', 'port' => 11211, 'weight' => 100]]);
        try {
            $conn->connect();
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);

            return;
        }

        $this->fail('oups');
    }
}
