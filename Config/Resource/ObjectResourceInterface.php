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
 * @interface ObjectResourceInterface extends ResourceInterface
 * @see ResourceInterface
 *
 * @package Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
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
