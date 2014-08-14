<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package\Exception;

/**
 * @class RequirementConflictException extends \LogicException
 * @see \LogicException
 *
 * @package Selene\Module\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RequirementConflictException extends \LogicException
{
    /**
     * missingPackage
     *
     * @param string $parent
     * @param string $missing
     *
     * @return RequirementConflictException
     */
    public static function missingPackage($parent, $missing)
    {
        return new static(
            sprintf('Package "%1$s" requires package "%2$s", but package "%s" doesn\'t exist.', $parent, $missing)
        );
    }

    /**
     * circularReference
     *
     * @param string $parent
     * @param string $conflict
     *
     * @return RequirementConflictException
     */
    public static function circularReference($parent, $conflict)
    {
        return new static(
            sprintf(
                'Circular reference error: Package "%1$s" requires "%2$s" wich requires "%1$s".',
                $parent,
                $conflict
            )
        );
    }
}
