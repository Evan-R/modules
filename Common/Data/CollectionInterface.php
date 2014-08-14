<?php

/**
 * This File is part of the Selene\Module\Common\Data package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Data;

/**
 * @class CollectionInterface
 * @package Selene\Module\Common\Data
 * @version $Id$
 */
interface CollectionInterface
{
    public function initialize(array $data);

    public function set($attribute, $value);

    public function get($attribute, $default = null);

    public function has($attribute);

    public function delete($attribute = null);

    public function all();
}
