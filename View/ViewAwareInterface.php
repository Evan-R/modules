<?php

/**
 * This File is part of the Selene\Module\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View;

/**
 * @interface ViewAwareInterface
 *
 * @package Selene\Module\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ViewAwareInterface
{
    public function setView(ManagerInterface $view);

    public function getView();
}
