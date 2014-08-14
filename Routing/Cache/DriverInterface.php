<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

/**
 * @interface StorageInterface
 * @package Selene\Module\Routing
 * @version $Id$
 */
interface DriverInterface
{

    /**
     * put
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @access public
     * @return void
     */
    public function put($id, $content);

    /**
     * replace
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @access public
     * @return void
     */
    public function replace($id, $content);

    /**
     * remove
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function remove($id);

    /**
     * get
     *
     * @param mixed $id
     *
     * @access public
     * @return mixed
     */
    public function get($id);

    /**
     * has
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function has($id);
}
