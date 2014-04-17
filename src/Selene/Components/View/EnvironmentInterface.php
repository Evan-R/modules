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
 * @interface EnvironmentInterface
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface EnvironmentInterface
{
    /**
     * render
     *
     * @param mixed $template
     * @param mixed $context
     *
     * @access public
     * @return string
     */
    public function render($template, array $context = []);

    /**
     * registerEngine
     *
     * @param EngineInterface $engine
     *
     * @access public
     * @return void
     */
    public function registerEngine(EngineInterface $engine);
}
