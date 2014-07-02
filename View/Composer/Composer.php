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
 * @class Composer
 * @package Selene\Components\View\Composer
 * @version $Id$
 */
class Composer implements ComposerInterface
{
    private $context;

    /**
     * compose
     *
     * @param mixed $template
     * @param mixed $context
     *
     * @return void
     */
    public function compose($template, Composable $composable)
    {
        $this->context[(string)$template] = &$composable;
    }

    /**
     * has
     *
     * @param mixed $template
     *
     * @return boolean
     */
    public function has($template)
    {
        return isset($this->context[(string)$template]);
    }

    /**
     * render
     *
     * @param mixed $template
     * @param mixed $context
     *
     * @return string
     */
    public function render(View $view, $template, $context = [])
    {
        if ($this->has($template)) {
            return $this->context[(string)$template]->render($view, $template, $context);
        }
    }
}
