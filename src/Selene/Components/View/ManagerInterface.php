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

use \Selene\Components\View\Template\EngineInterface;

/**
 * @interface ManagerInterface
 * @package Selene\Components\View
 * @version $Id$
 */
interface ManagerInterface
{
    public function registerEngine(EngineInterface $engine);

    public function findEngine($template);
}
