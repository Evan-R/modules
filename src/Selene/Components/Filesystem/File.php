<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem;

/**
 * @class File
 * @package
 * @version $Id$
 */
class File extends AbstractFileObject
{
    public function setPath($path)
    {
        if (!$this->files->isFile($path)) {
            throw new IOException(sprintf('%s is not a file', $path));
        }
        $this->path = $path;
    }

    public function chmod($permissions = 0644)
    {
        $this->files->chmod((string)$this, $permissions);
        return $this;
    }

    public function chgrp($group)
    {
        $this->files->chmod((string)$this, $group);
        return $this;
    }

    public function chown($owner)
    {
        $this->files->chown((string)$this, $owner);
        return $this;
    }
}
