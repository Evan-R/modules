<?php

/**
 * This File is part of the Selene\Components\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

/**
 * @interface LoaderInterface
 * @package Selene\Components\View\Template
 * @version $Id$
 */
interface LoaderInterface
{

    public function load($template);

    public function isValid($template, $time);
}
