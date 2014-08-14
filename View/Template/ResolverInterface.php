<?php

/**
 * This File is part of the Selene\Module\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Template;

/**
 * @interface ResolverInterface
 *
 * @package Selene\Module\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ResolverInterface
{
    /**
     * resolver
     *
     * @param mixed $template
     *
     * @access public
     * @return string
     */
    public function resolve($template);
}
