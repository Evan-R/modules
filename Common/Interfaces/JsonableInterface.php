<?php

/**
 * This File is part of the Selene\Components\Common\Interfaces package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Interfaces;

/**
 * @class Jsonable
 * @package
 * @version $Id$
 */
interface JsonableInterface
{
    public function toJson();
}