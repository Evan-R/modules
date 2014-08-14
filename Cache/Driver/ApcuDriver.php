<?php

/**
 * This File is part of the Selene\Module\Cache\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Driver;

/**
 * @class ApcuDriver
 * @package Selene\Module\Cache\Driver
 * @version $Id$
 */
class ApcuDriver extends ApcDriver
{
    /**
     * {@inheritdoc}
     */
    public function cachedItemExists($key)
    {
        return apcu_exists($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromCache($key)
    {
        return apcu_fetch($key);
    }

    /**
     * {@inheritdoc}
     */
    public function writeToCache($key, $data, $expires = 60, $compressed = false)
    {
        return apcu_store($key, $data, $expires);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFromCache($key)
    {
        return apcu_delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flushCache()
    {
        return apcu_clear_cache('user');
    }

    /**
     * {@inheritdoc}
     */
    protected function incrementValue($key, $value)
    {
        return apcu_inc($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function decrementValue($key, $value)
    {
        return apcu_dec($key, $value);
    }
}
