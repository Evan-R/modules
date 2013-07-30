<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Stream\Library\Tests\Cache;

use Stream\Cache\Storage;
use Stream\Cache\Driver\DriverAPC;

/**
 * Class: StorageMemcachedTest
 *
 * @see \TestCases
 */
class StorageAPCTest extends StorageTestCases
{
    /**
     * setUp
     *
     * @access protected
     * @return void
     */
    protected function setUp()
    {
        $driver = new DriverAPC();
        $this->cache = new Storage($driver, 'apccache');
    }

    /**
     * tearDown
     *
     * @access protected
     * @return void
     */
    protected function tearDown()
    {
        //$this->object->purge();
    }
}
