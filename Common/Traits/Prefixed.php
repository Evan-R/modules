<?php

/*
 * This File is part of the Selene\Module\Common\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Traits;

/**
 * @class Prefixed
 * @package Selene\Module\Common\Traits
 * @version $Id$
 */
trait Prefixed
{
    protected $prefix;

    /**
     * getPrefixed
     *
     * @param mixed $key
     *
     * @access public
     * @return string
     */
    public function getPrefixed($key)
    {
        return $this->prefix.$key;
    }
}
