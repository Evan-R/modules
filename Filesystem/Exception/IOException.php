<?php

/**
 * This File is part of the Selene\Module\Filesystem\Exception package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Exception;

/**
 * @class IOException
 * @see \RuntimeException
 *
 * @package Selene\Module\Filesystem\Exception
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class IOException extends \RuntimeException
{
    public static function gidError($group)
    {
        return new self(sprintf('Group %s does not exist.', $group));
    }

    public static function uidError($user)
    {
        return new self(sprintf('User %s does not exist.', $user));
    }

    public static function chmodError($file)
    {
        return new self(sprintf('Permissions on %s could not be set.', $file));
    }

    public static function chownError($file, $linkError = false)
    {
        return new self(sprintf('Could not change owner on %s%s.', $linkError ? 'link ' : '', $file));
    }

    public static function chgrpError($file, $linkError = false)
    {
        return new self(sprintf('Could not change group on %s%s.', $linkError ? 'link ' : '', $file));
    }
}
