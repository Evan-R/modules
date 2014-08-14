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
 * @class SeparatorParserInterface
 * @package Selene\Module\Common
 * @version $Id$
 */
interface SeparatorParserInterface
{
    public function parse($string);

    public function supports($string);
}
