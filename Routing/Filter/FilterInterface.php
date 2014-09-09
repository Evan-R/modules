<?php

/*
 * This File is part of the Selene\Module\Routing\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Filter;

use \Selene\Module\Routing\Event\RouteFilter;

/**
 * @interface FilterInterface
 *
 * @package Selene\Module\Routing\Filter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface FilterInterface
{
    const T_BEFORE = 1001;

    const T_AFTER  = 1002;

    /**
     * Get the filter type.
     *
     * @return int
     */
    public function getType();

    /**
     * getName
     *
     * @return string
     */
    public function getName();

    /**
     * run
     *
     * @return mixed|null
     */
    public function run(RouteFilter $event);
}
