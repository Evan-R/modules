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

use \Selene\Components\Cache\Driver\MemcachedConnection as Connection;

class MemcachedConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('\Selene\Components\Cache\Driver\ConnectionInterface', new Connection);
    }

    /** @test */
    public function itShouldConnectToServer()
    {
        $conn = new Connection(new \Memcached, [['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]]);

        $this->assertFalse($conn->isConnected());

        $conn->connect();

        $this->assertTrue($conn->isConnected());

    }

    /** @test */
    public function itShouldCloseConnections()
    {
        $conn = new Connection(new \Memcached, [['host' => '127.0.0.1', 'port' => 11211, 'weight' => 100]]);

        $conn->connect();
        $this->assertTrue($conn->isConnected());

        if ($conn->close()) {
            $this->assertFalse($conn->isConnected());
        }
    }

    /** @test */
    public function itShouldThrowExceptionOnConnectionFailure()
    {
        $conn = new Connection($mc = new \Memcached, [['host' => null, 'port' => 11211, 'weight' => 100]]);

        $mc->quit();

        try {
            $conn->connect();
        } catch (\RuntimeException $e) {
            $this->assertTrue(true);

            return;
        }

        if ($conn->isConnected()) {
            $this->markTestSkipped();
        }

        $this->fail('oups');
    }
}
