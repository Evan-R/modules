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

use \Selene\Module\View\Template\EngineInterface;

/**
 * @interface ManagerInterface
 * @package Selene\Module\View
 * @version $Id$
 */
interface DispatcherInterface
{
    public function dispatch($template, array $context = [], array $merge = null);

    public function findEngineByName($name);

    public function findEngineByTemplate($template);
}
