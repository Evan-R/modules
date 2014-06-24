<?php

/**
 * This File is part of the Selene\Components\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @interface ObjectResourceInterface
 * @package Selene\Components\Config\Resource
 * @version $Id$
 */
interface ObjectResourceInterface extends ResourceInterface
{
    /**
     * getObjectReflection
     *
     *
     * @access public
     * @return \ReflectionObject
     */
    public function getObjectReflection();
}
