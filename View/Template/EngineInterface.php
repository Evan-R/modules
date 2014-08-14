<?php

/**
 * This File is part of the Selene\Module\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Template;

/**
 * @interface EngineInterface
 *
 * @package Selene\Module\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface EngineInterface
{
    public function render($template, array $context = []);

    public function exists($name);

    public function supports($name);

    public function getType();
}
