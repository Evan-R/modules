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

/**
 * @interface SectionAcacheInterface
 *
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface SectionCacheInterface
{
    /**
     * has
     *
     * @param mixed $path
     *
     * @return boolean
     */
    public function has($path);

    /**
     * get
     *
     * @param mixed $path
     *
     * @return array
     */
    public function get($path);

    /**
     * put
     *
     * @param mixed $path
     * @param array $routeNames
     *
     * @return void
     */
    public function put($path, $routeNames);
}
