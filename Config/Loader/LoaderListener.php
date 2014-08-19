<?php

/*
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Loader;

/**
 * @interface LoaderListener
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface LoaderListener
{
    public function onLoaded($resource);
}
