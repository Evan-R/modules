<?php

/**
 * This File is part of the Selene\Components\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel;

/**
 * @class ApplicationInterface
 * @package Selene\Components\Kernel
 * @version $Id$
 */
interface ApplicationInterface
{
    public function boot();

    public function getLoadedPackages();
}
