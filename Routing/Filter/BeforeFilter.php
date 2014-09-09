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

/**
 * @class BeforeFilter
 *
 * @package Selene\Module\Routing\Filter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class BeforeFilter extends AbstractFilter
{
    final public function getType()
    {
        return self::T_BEFORE;
    }
}
