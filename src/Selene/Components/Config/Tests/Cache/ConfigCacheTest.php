<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Cache;

use \Selene\Components\TestSuite\TestCase;
use \Selene\Components\Config\Cache\ConfigCache;

/**
 * @class ConfigCacheTest extends TestCase
 * @see TestCase
 *
 * @package Selene\Components\Config\Tests\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ConfigCacheTest extends TestCase
{
    public function testIsValid()
    {
        $cache = new ConfigCache('/foo/file');
        $cache->setDebug(false);

        $this->assertFalse($cache->isValid());
    }
}
