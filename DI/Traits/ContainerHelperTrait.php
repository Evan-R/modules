<?php

/**
 * This File is part of the Selene\Components\DI\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Traits;

/**
 * @class ContainerHelperTrait
 * @package Selene\Components\DI\Traits
 * @version $Id$
 */
trait ContainerHelperTrait
{
    /**
     * camelCaseStr
     *
     * @param string $str
     *
     * @return string
     */
    protected function camelCaseStr($str)
    {
        return StringHelper::strCamelCaseAll($str, ['_' => ' ', ':' => ' ', '.' => 'Nss ', '\\' => 'Dbs ']);
    }

    /**
     * inScopes
     *
     * @param string $needle the scope to check against other scopes.
     * @param string $heystack a given scope or a scope range.
     *
     * @return boolean
     */
    protected function inScopes($needle, $heystack)
    {
        return $needle === ($needle & $heystack);
    }
}
