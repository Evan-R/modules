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
 * @interface EngineInterface
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface EngineInterface
{
    /**
     * render
     *
     * @param mixed $view
     * @param mixed $context
     *
     * @access public
     * @return string
     */
    public function render($file, array $context = []);

    /**
     * supports
     *
     *
     * @access public
     * @return boolean
     */
    public function supports($extension);
}
