<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common;

/**
 * IOHandlerInterface provides an interface for manipulating
 * input data, e.g. de/compression, de/encryption.
 *
 * @interface IOHandlerInterface
 *
 * @package Selene\Components\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface IOProxyInterface
{
    /**
     * Read in data.
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function in($data);

    /**
     * Write out data.
     *
     * @param mixed $data
     *
     * @access public
     * @return mixed
     */
    public function out($data);
}
