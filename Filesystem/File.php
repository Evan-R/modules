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

use Selene\Components\Filesystem\Exception\IOException;

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
        $this->files->chgrp((string)$this, $group);
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
    public function touch($time = null, $atime = null)
    {
        $this->files->touch((string)$this, $time, $atime);
    }

    /**
     * toArray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $file = new SplFileInfo($this->path);
        return $file->toArray();
    }
}
