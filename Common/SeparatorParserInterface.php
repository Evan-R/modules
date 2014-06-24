<?php

/**
 * This File is part of the Selene\Components\Common package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common;

/**
 * @class SeparatorParserInterface
 * @package Selene\Components\Common
 * @version $Id$
 */
interface SeparatorParserInterface
{
    public function parse($string);

    public function supports($string);
}
