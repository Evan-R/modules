<?php

/**
 * This File is part of the Selene\Components\Cache\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Tests;

use Selene\Components\Cache\Storage;
use Selene\Components\Cache\Driver\ApcDriver;

class StorageApcTest extends StorageTestCase
{
    protected function setUp()
    {
        // if in hhvm or if cli usage is not set, skip tests
        if (false !== strrpos(PHP_VERSION, 'hiphop') || !ini_get('apc.enable_cli')) {
            $this->markTestSkipped('Environment doesn\'t support APC');
        }

        $this->cache = new Storage(new ApcDriver);
    }
}
