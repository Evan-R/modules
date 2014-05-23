<?php

/**
 * This File is part of the Selene\Components\DI\Meta package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Meta;


/**
 * @class MetaDataInterface
 * @package Selene\Components\DI\Meta
 * @version $Id$
 */
interface MetaDataInterface
{
    public function getName();
    public function get($parameter, $default = null);
}
