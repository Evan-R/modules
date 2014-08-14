<?php

/**
 * This File is part of the Selene\Module\Cache\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Tests;

use Memcache;
use Selene\Module\Cache\Storage;
use Selene\Module\Cache\Driver\MemcacheDriver;
use Selene\Module\Cache\Driver\MemcacheConnection;

class StorageMemcacheTest extends StorageTestCase
{
    protected $memcache;

    protected $connection;

    /**
     * setUp
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        if (!extension_loaded('memcache')) {
            $this->markTestSkipped('Environment doesn\'t support Memcache');
        }

        $servers = [
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 100
            ]
            ];

        $connection = new MemcacheConnection(new Memcache('memcache_test'), $servers);

        $this->connection = $connection;
        $this->cache = new Storage(new MemcacheDriver($connection), 'mycache');
    }

    /**
     * tearDown
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ($this->connection) {

            $this->connection->getDriver()->flush();

            $this->connection->close();
        }
    }
}
