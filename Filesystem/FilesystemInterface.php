<?php

/**
 * This File is part of the Selene\Module\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem;

/**
 * @interface FilesystemInterface
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface FilesystemInterface
{
    /**
     * @var string
     */
    const COPY_PREFIX = 'copy';

    /**
     * @var int
     */
    const COPY_START_OFFSET = 1;

    /**
     * Creates a directory.
     *
     * @param string  $dir the path to the directory to be Created.
     * @param int     $pmask the permission level expressed as a 4 digit hex
     * mask
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function mkdir($dir, $pmask = 0755, $recursive = true);

    /**
     * Removes a directory entirely.
     *
     * @param string $directory source path ot hthe directory to be removed.
     *
     * @return boolean
     */
    public function rmdir($directory);

    /**
     * Removes all conatining files and directories within the given directory.
     *
     * Flushin a directory will keep the directory itself, but removes all
     * containing items.
     *
     * @param string $directory source path ot hthe directory to be flushed.
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return void
     */
    public function flush($directory);

    /**
     * Touches a file.
     *
     * Will update ad file's  temestamp or create a new file if the given file
     * doesn't exists.
     *
     * @param string  $file  the file path.
     * @param integer $time  the modifytime as unix timestamp.
     * @param integer $atime the accesstime as unix timestamp.
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return boolean true if touching of the file was successful.
     */
    public function touch($file, $time = null, $atime = null);

    /**
     * Deletes a file.
     *
     * @param string $file the file path.
     *
     * @return boolean
     */
    public function unlink($file);

    /**
     * Removes files or directories.
     *
     * @param string|array $file source paths that should be removed.
     *
     * @return boolean returns true if all files where deleted successfully,
     * and false if some or one file coulnd not be deleted.
     */
    public function remove($file);

    /**
     * Copies a file or directory to a target destination.
     *
     * If $target is null, copy() will copy the file or directory to its
     * parent directory and will enumerate its name.
     *
     * @param string  $source
     * @param string  $target
     * @param boolean $replace
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return integer returns the total count of bytes that where copied.
     */
    public function copy($source, $target = null, $replace = false);

    /**
     * Set file permissions.
     *
     * @param string  $file
     * @param integer $permission
     * @param boolean $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException if permissions
     * could not be set.
     *
     * @return boolean true
     */
    public function chmod($file, $permission = 0755, $recursive = true, $umask = 0000);

    /**
     * Change file ownership.
     *
     * @param string     $file
     * @param string|int $owner
     * @param boolean    $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException if UID doesn't
     * exist.
     * @throws \Selene\Module\Filesystem\Exception\IOException if ownership
     * could not be changed.
     * @return boolean true
     */
    public function chown($file, $owner, $recursive = true);

    /**
     * Change file group.
     *
     * @param string     $file
     * @param string|int $group
     * @param boolean    $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException if GID doesn't
     * exist.
     * @throws \Selene\Module\Filesystem\Exception\IOException if group
     * could not be changed.
     *
     * @return boolean true
     */
    public function chgrp($file, $group, $recursive = true);

    /**
     * exists
     *
     * @param mixed $param
     *
     * @return boolean
     */
    public function exists($file);

    /**
     * isDir
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isDir($file);

    /**
     * isFile
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isFile($file);

    /**
     * isLink
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isLink($file);

    /**
     * Ensure that a directory exists.
     *
     * Will create the dircetory if needed.
     *
     * @param string $file
     *
     * @return void
     */
    public function ensureDirectory($dir);

    /**
     * Ensure that a file exists.
     *
     * Will create the file if needed.
     *
     * @param string $file
     *
     * @return void
     */
    public function ensureFile($file);
}
