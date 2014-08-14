<?php

/**
 * This File is part of the Selene\Module\View\Composer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Composer;

use \Selene\Module\View\ManagerInterface as View;

/**
 * @interface Composeable
 * @package Selene\Module\View\Composer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface Composable
{
    public function compose(Context $context);
}
