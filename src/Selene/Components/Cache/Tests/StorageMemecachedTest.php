<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Tests;

use Memcached;
use Selene\Components\Cache\Storage;
use Selene\Components\Cache\Driver\MemcachedDriver;
use Selene\Components\Cache\Driver\MemcachedConnection;

/**
 * Class: StorageMemcachedTest
 *
 * @see \TestCases
 */
class StorageMemcachedTest extends StorageTestCase
{
    protected $memcached;

    /**
     * setUp
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $servers = [
            [
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 100
            ]
            ];

        $connection = new MemcachedConnection(new Memcached('fooish'), $servers);

        $this->cache = new Storage(new MemcachedDriver($connection), 'mycache');
    }

    /**
     * tearDown
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        //$this->cache->purge();
        parent::tearDown();
    }
}
