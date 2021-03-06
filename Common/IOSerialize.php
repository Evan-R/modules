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
 * @class IOSerialize implements IOProxyInterface
 * @see IOProxyInterface
 *
 * @package Selene\Module\Common
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
        if (false !== ($data = @unserialize($data))) {
            return $data;
        }

        return null;
    }
}
