<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

/**
 * @interface BuilderInterface
 * @package Selene\Components\DI
 * @version $Id$
 */
interface BuilderInterface
{
    /**
     * getContainer
     *
     * @access public
     * @return mixed
     */
    public function getContainer();

    public function getProcessor();
}
