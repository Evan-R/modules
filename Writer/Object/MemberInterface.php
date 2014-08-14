<?php

/**
 * This File is part of the Selene\Module\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Object;

/**
 * @interface MemberInterface
 * @package Selene\Module\Writer
 * @version $Id$
 */
interface MemberInterface
{
    const IS_PUBLIC    = 'public';
    const IS_PROTECTED = 'protected';
    const IS_PRIVATE   = 'private';

    const T_VOID       = 'void';
    const T_STRING     = 'string';
    const T_BOOL       = 'boolean';
    const T_INT        = 'integer';
    const T_FLOAT      = 'float';
    const T_ARRAY      = 'array';
    const T_MIXED      = 'mixed';
}
