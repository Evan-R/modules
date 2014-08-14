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
 * @class TemplateInterface
 * @package Selene\Module\View\Template
 * @version $Id$
 */
interface TemplateInterface
{
    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName();

    /**
     * Return the Template engine alias.
     *
     * @access public
     * @return string
     */
    public function getEngine();
}
