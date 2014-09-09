<?php

/*
 * This File is part of the Selene\Module\Routing\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Loader;

use \Selene\Module\Config\Loader\DelegatingLoader as BaseDelegatingLoader;

/**
 * @class DelegatingLoader
 * @package Selene\Module\Routing\Loader
 * @version $Id$
 */
class DelegatingLoader extends BaseDelegatingLoader
{
    public function getRouteBuilder()
    {
    }
}
