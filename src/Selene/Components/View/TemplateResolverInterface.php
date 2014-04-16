<?php

/**
 * This File is part of the Selene\Components\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View;

/**
 * @interface TemplateResolverInterface
 * @package Selene\Components\View
 * @version $Id$
 */
interface TemplateResolverInterface
{
    public function resolve($template);

    public function setPackagePaths(array $paths);

    public function getPackagePaths();

    public function getPackagePath($package = null);
}
