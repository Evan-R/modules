<?php

/**
 * This File is part of the Selene\Module\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer;

/**
 * @interface GeneratorInterface
 * @package Selene\Module\Writer
 * @version $Id$
 */
interface GeneratorInterface
{
    const RV_STRING = false;
    const RV_WRITER = true;

    public function generate($raw = self::RV_STRING);
}
