<?php

/**
 * This File is part of the Selene\Components\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

use \Selene\Components\Common\SeparatorParser;

/**
 * @class PathParser extends SeparatorParser PathParser
 * @see SeparatorParser
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PathParser extends SeparatorParser
{
    public function parse($string)
    {
        list($package, $subpath, $template) = parent::parse($string);

        return [$package, strtr($subpath, ['.' => DIRECTORY_SEPARATOR]), $template];
    }
}
