<?php

/**
 * This File is part of the Selene\Components\View\Composer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Composer;

use \Selene\Components\View\ManagerInterface as View;

/**
 * @interface Composeable
 * @package Selene\Components\View\Composer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface Composable
{
    /**
     * render
     *
     * @param ManagetInterface $view
     * @param array $context
     *
     * @return string
     */
    public function render(View $view, $template, $context = []);
}
