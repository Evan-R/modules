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
 * @class IOSerialize implements IOProxyInterface
 * @see IOProxyInterface
 *
 * @package Selene\Components\Common
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class IOSerialize implements IOProxyInterface
{
    /**
     * {@inheritdoc}
     */
    public function in($data)
    {
        return serialize($data);
    }

    /**
     * {@inheritdoc}
     */
    public function out($data)
    {
        return unserialize($data);
    }
}
