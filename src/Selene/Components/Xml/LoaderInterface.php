<?php

/**
 * This File is part of the Selene\Components\Xml package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml;

/**
 * @interface LoaderInterface
 * @package Selene\Components\Xml
 * @version $Id$
 */
interface LoaderInterface
{
    public function load($xml);

    public function setOption($option, $value);

    public function getOption($option, $default = null);

    public function getErrors();

}
