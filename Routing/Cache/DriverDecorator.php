<?php

/*
 * This File is part of the Selene\Module\Routing\Cache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

use \Selene\Module\Cache\Driver\DriverInterface as BaseDriver;

/**
 * @class DriverDecorator
 *
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class DriverDecorator implements DriverInterface
{
    /**
     * driver
     *
     * @var Selene\Module\Cache\Driver\DriverInterface
     */
    private $driver;

    /**
     * Constructor
     *
     * @param BaseDriver $driver
     */
    public function __construct(BaseDriver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->driver->cachedItemExists($id);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return $this->driver->getFromCache($id);
    }

    /**
     * {@inheritdoc}
     */
    public function put($id, $content)
    {
        $this->writeToCache($id.'.lastmod', time(), 0, true);
        $this->writeToCache($id, $content, 0, true);
    }

    /**
     * {@inheritdoc}
     */
    public function replace($id, $contnent)
    {
        return $this->put($id, $content);
    }

    /**
     * {@inheritdoc}
     */
    public function getModTime($id)
    {
        return $this->driver->getFromCache($id.'.lastmod') ?: time();
    }
}
