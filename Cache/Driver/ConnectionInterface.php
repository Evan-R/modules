<?php

/**
 * This File is part of the Selene\Module\Cache\Driver package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Cache\Driver;

/**
 * @interface ConnectionInterface
 * @package Selene\Module\Cache\Driver
 * @version $Id$
 */
interface ConnectionInterface
{
    public function connect();

    public function close();

    public function isConnected();

    public function getDriver();
}
