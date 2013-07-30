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
    /**
     * setPath
     *
     * @param mixed $path
     *
     * @access protected
     * @return void
     */
    protected function setPath($path)
    {
        if (!$this->files->isFile($path)) {
            throw new IOException(sprintf('%s is not a file', $path));
        }
        $this->path = $path;
    }

    /**
     * chmod
     *
     * @param int $permissions
     *
     * @access public
     * @return void
     */
    public function chmod($permissions = 0644)
    {
        $this->files->chmod((string)$this, $permissions);
        return $this;
    }

    /**
     * chgrp
     *
     * @param mixed $group
     *
     * @access public
     * @return void
     */
    public function chgrp($group)
    {
        $this->files->chmod((string)$this, $group);
        return $this;
    }

    /**
     * chown
     *
     * @param mixed $owner
     *
     * @access public
     * @return void
     */
    public function chown($owner)
    {
        $this->files->chown((string)$this, $owner);
        return $this;
    }

    /**
     * touch
     *
     * @param mixed $time
     * @param mixed $atime
     *
     * @access public
     * @return void
     */
    public function touch($time, $atime)
    {
        $this->files->touch((string)$this, $time, $atime);
    }
}
