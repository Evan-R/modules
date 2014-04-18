<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

/**
 * @class Alias Alias
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Alias
{
    /**
     * @param string  $alias
     * @param string  $id
     * @param boolean $internal
     *
     * @access public
     */
    public function __construct($id, $internal = false)
    {
        $this->id = $id;
        $this->setInternal($internal);
    }

    /**
     * setInternal
     *
     * @param mixed $internal
     *
     * @access public
     * @return void
     */
    public function setInternal($internal)
    {
        $this->internal = (bool)$internal;
    }

    /**
     * isInternal
     *
     * @access public
     * @return bool
     */
    public function isInternal()
    {
        return $this->internal;
    }

    /**
     * __toString
     *
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
}
