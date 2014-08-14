<?php

/**
 * This File is part of the Selene\Module\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Kernel;

use \Symfony\Component\HttpFoundation\Request;

/**
 * @class ApplicationInterface
 * @package Selene\Module\Kernel
 * @version $Id$
 */
interface ApplicationInterface
{
    /**
     * Boot the application
     *
     * @return void
     */
    public function boot();

    /**
     * Starts the application
     *
     * @return void
     */
    public function run(Request $request = null);

    /**
     * getLoadedPackages
     *
     * @return array
     */
    public function getPackages();

    /**
     * Get application version.
     *
     * @return string
     */
    public static function version();
}
