<?php

/**
 * This File is part of the Selene\Module\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common;

/**
 * @class Separators
 * @package Selene\Module\Common
 * @version $Id$
 */
class Separators
{
    /**
     * @var string
     */
    const PATH_SEPARATOR = PATH_SEPARATOR;

    /**
     * @var string
     */
    const PACKAGE_SEPARATOR = ':';

    /**
     * @var string
     */
    const NAMESPACE_SEPARATOR = '\\';

    /**
     * @access private
     */
    private function __construct()
    {
    }
}
