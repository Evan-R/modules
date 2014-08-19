<?php

/*
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common;

/**
 * Passes through input data
 *
 * @class IOPassThrough implements IOProxyInterface
 * @see IOHandlerInterface
 *
 * @package Selene\Module\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IOPassThrough implements IOProxyInterface
{
    /**
     * in
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function in($data)
    {
        return $data;
    }

    /**
     * out
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function out($data)
    {
        return $data;
    }
}
