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

use \FilesystemIterator;
use \Selene\Module\Filesystem\Exception\IOException;
use \Selene\Module\Filesystem\Traits\FsHelperTrait;
use \Selene\Module\Filesystem\Traits\PathHelperTrait;

/**
 * @class Filesystem implements FilesystemInterface
 * @see FilesystemInterface
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Filesystem implements FilesystemInterface
{
    use PathHelperTrait, FsHelperTrait {
        FsHelperTrait::mask as public getMask;
    }

    /**
     * file
     *
     * @var string
     */
    protected $file;

    /**
     * dir
     *
     * @var string
     */
    protected $dir;

    /**
     * filePermissions
     *
     * @var int
     */
    protected $filePermissions;

    /**
     * directoryPermissions
     *
     * @var int
     */
    protected $directoryPermissions;

    /**
     * currentExclude
     *
     * @var mixed
     */
    protected $currentExclude;

    /**
     * currentFilter
     *
     * @var mixed
     */
    protected $currentFilter;

    /**
     * copyPrefix
     *
     * @var string
     */
    protected $copyPrefix;

    /**
     * copyStartOffset
     *
     * @var int
     */
    protected $copyStartOffset;

    /**
     * Creates a new Filesystem instance.
     *
     * @param int $directoryPermissions
     * @param int $filePermissions
     *
     */
    public function __construct($directoryPermissions = 0755, $filePermissions = 0644)
    {
        $this->directoryPermissions  = $directoryPermissions;
        $this->filePermissions       = $filePermissions;
    }

    /**
     * create
     *
     * @return Filesystem
     */
    public function create()
    {
        return new static($this->directoryPermissions, $this->filePermissions);
    }

    /**
     * Create a directory
     *
     * @param string  $dir
     * @param integer $permissions
     * @param boolean $recursive
     *
     * @throws IOException
     *
     * @return void
     */
    public function mkdir($dir, $permissions = 0755, $recursive = true)
    {
        if (true !== @mkdir($dir, $permissions, $recursive)) {
            throw new IOException(sprintf('[mkdir]: error creating directory "%s".', $dir));
        }
    }

    /**
     * Removes a directory.
     *
     * @param string $dir the directory path.
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return void
     */
    public function rmdir($directory)
    {
        try {
            $this->rmRecursive($directory);
            rmdir($directory);
        } catch (\Exception $e) {
            throw new IOException(sprintf('[rmdir]: error remoing directory "%s".', $directory));
        }
    }

    /**
     * Removes all conatining files and directories within the given directory.
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return void
     */
    public function flush($directory)
    {
        if (!$this->isDir($directory)) {
            throw new IOException(sprintf('%s is not a directory', $directory));
        }

        try {
            $this->rmRecursive($directory);
        } catch (\Exception $e) {
            throw new IOException(sprintf('could not flush directory %s', $directory));
        }
    }

    /**
     * Check if a directory is empty.
     *
     * @param string $directory path to the directory.
     *
     * @return boolean
     */
    public function isEmpty($directory)
    {
        foreach ($this->getIterator($directory) as $file) {
            return false;
        }

        return true;
    }

    /**
     * Touches a file.
     *
     * @param string  $file  the file path.
     * @param integer $time  the modifytime as unix timestamp.
     * @param integer $atime the accesstime as unix timestamp.
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return boolean true if touching of the file was successful.
     */
    public function touch($file, $time = null, $atime = null)
    {
        if (true !== ($time ? @touch($file, $time, $atime) : @touch($file))) {
            throw new IOException(sprintf('could not touch file %s', $file));
        }

        return true;
    }

    /**
     * Deletes a file.
     *
     * @param string $file the file path.
     *
     * @return boolean
     */
    public function unlink($file)
    {
        return unlink($file);
    }

    /**
     * Remove
     *
     * @param string|array $file
     *
     * @return boolean returns true if all files where deleted successfully,
     * and false if some or one file coulnd not be deleted.
     */
    public function remove($file)
    {
        $result = 1;

        foreach ($this->ensureTraversable($file) as $item) {
            if ($this->isFile($item)) {
                $result = $result & (int)$this->unlink($item);
            }

            if ($this->isDir($item)) {
                $result = $result & (int)$this->rmdir($item);
            }
        }

        return 0 !== $result;
    }

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
    public function copy($source, $target = null, $replace = false)
    {
        if ($this->isDir($source)) {
            return $this->copyDir($source, $target, $replace);
        }

        if (!$this->isFile($source)) {
            throw new IOException(sprintf('%s: no such file or directory', $source));
        }

        $target = $this->validateCopyTarget($source, $target, $replace);

        $this->ensureDirectory(dirname($target));

        return $this->doCopyFile($source, $target);
    }

    /**
     * copyDir
     *
     * @param string $source
     * @param string $target
     * @param boolean $replace
     * @param boolean $followLinks
     *
     * @return integer
     */
    public function copyDir($source, $target = null, $replace = false, $followLinks = false)
    {
        if (!$this->isDir($source)) {
            throw new IOException(sprintf('"%s" is not a directory', $source));
        }

        $target = $this->validateCopyTarget($source, $target, $replace);

        $flags = FilesystemIterator::SKIP_DOTS;

        if ($followLinks) {
            $flags = $flags | FilesystemIterator::FOLLOW_SYMLINKS;
        }

        return $this->doCopyDir($source, $target, $flags);
    }

    /**
     * Set file permissions.
     *
     * @param string  $file
     * @param integer $permission
     * @param boolean $recursive
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return boolean true
     */
    public function chmod($file, $permission = 0755, $recursive = true, $umask = 0000)
    {
        foreach ($this->ensureTraversable($file) as $item) {

            if (!$this->isLink($item) && $this->isDir($item) && $recursive) {
                $this->chmod(
                    $this->getIterator($item, FilesystemIterator::CURRENT_AS_PATHNAME),
                    $permission,
                    true,
                    $umask
                );
            }

            if (true !== @chmod($item, $permission & ~$umask)) {
                throw IOException::chmodError($item);
            }
        }

        return true;
    }

    /**
     * chown
     *
     * @param mixed $param
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return boolean
     */
    public function chown($file, $owner, $recursive = true)
    {
        if (!$this->uidExists($owner)) {
            throw IOException::uidError($owner);
        }

        foreach ($this->ensureTraversable($file) as $item) {
            if ($this->isLink($item) && function_exists('lchown')) {

                if (true !== @lchown($item, $owner)) {
                    throw IOException::chownError($item, true);
                }

                continue;
            }

            if ($recursive && !$this->isLink($item) && $this->isDir($item)) {
                $this->chown($this->getIterator($item, FilesystemIterator::CURRENT_AS_PATHNAME), $owner, true);
            }

            if (true !== @chown($item, $owner)) {
                throw IOException::chownError($item);
            }
        }

        return true;
    }

    /**
     * chgrp
     *
     * @param mixed $file
     * @param mixed $group
     *
     * @throws \Selene\Module\Filesystem\Exception\IOException
     *
     * @return boolean
     */
    public function chgrp($file, $group, $recursive = true)
    {
        if (!$this->gidExists($group)) {
            throw IOException::gidError($group);
        }

        foreach ($this->ensureTraversable($file) as $item) {
            if ($this->isLink($item) && function_exists('lchgrp')) {
                if (true !== @lchgrp($item, $owner)) {
                    throw IOException::chgrpError($item, true);
                }
                continue;
            }

            if ($recursive && !$this->isLink($item) && $this->isDir($item)) {
                $this->chgrp($this->getIterator($item, FilesystemIterator::CURRENT_AS_PATHNAME), $group, true);
            }

            if (true !== @chgrp($item, $group)) {
                throw IOException::chgrpError($item);
            }
        }

        return true;
    }

    /**
     * mask
     *
     * @param string $file
     * @param int    $mode
     *
     * @throws IOException if file is invalid.
     *
     * @return boolean
     */
    public function mask($file, $mode = null)
    {
        $cmask = $mode ?: ($this->isFile($file) ? 0666 : ($this->isDir($file) ? 0775 : $mode));

        if (null === $cmask) {
            throw new IOException(sprintf('%s is not a file or a directory', $file));
        }

        return $this->chmod($file, $this->getMask($cmask));
    }

    /**
     * exists
     *
     * @param mixed $param
     *
     * @return boolean
     */
    public function exists($file)
    {
        return $this->isDir($file) || $this->isFile($file);
    }

    /**
     * isDir
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isDir($file)
    {
        return stream_is_local($file) && is_dir($file);
    }

    /**
     * isFile
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isFile($file)
    {
        return stream_is_local($file) && is_file($file);
    }

    /**
     * isLink
     *
     * @param mixed $file
     *
     * @return boolean
     */
    public function isLink($file)
    {
        return stream_is_local($file) && is_link($file);
    }

    /**
     * Ensure that a directory exists.
     *
     * Will create the dircetory if needed.
     *
     * @param string $file
     *
     * @return void
     */
    public function ensureDirectory($dir)
    {
        if (!$this->isDir($dir)) {
            $this->mkdir($dir, $this->directoryPermissions, true);
        }
    }

    /**
     * Ensure that a file exists.
     *
     * Will create the file if needed.
     *
     * @param string $file
     *
     * @return void
     */
    public function ensureFile($file)
    {
        if (!$this->isFile($file)) {
            $this->ensureDirectory($dir = dirname($file));
            $this->touch($file);
        }
    }

    /**
     * Checks if a user id exists.
     *
     * @param string $uid
     *
     * @return boolean
     */
    public function uidExists($uid)
    {
        if (is_numeric($uid) && false !== @posix_getpwuid((int)$uid)) {
            return true;
        }

        return false !== @posix_getpwnam($uid);
    }

    /**
     * Checks if a goup id exists.
     *
     * @param sring $groupID
     *
     * @return boolean
     */
    public function gidExists($gid)
    {
        if (is_numeric($gid) && false !== @posix_getgrgid((int)$gid)) {
            return true;
        }

        return false !== @posix_getgrnam($gid);
    }

    /**
     * Get the file modification time.
     *
     * @param string $file
     *
     * @return integer
     */
    public function fileMTime($file)
    {
        return filemtime($file);
    }

    /**
     * Get the file access time.
     *
     * @param string $file
     *
     * @return integer
     */
    public function fileATime($file)
    {
        return fileatime($file);
    }

    /**
     * Get the file creation time.
     *
     * @param string $file
     *
     * @return integer
     */
    public function fileCTime($file)
    {
        return filectime($file);
    }

    /**
     * file
     *
     * @param string $file
     *
     * @return File
     */
    public function file($file)
    {
        return new File($this, $file);
    }

    /**
     * directory
     *
     * @param string $directory
     * @param int    $flags
     *
     * @return Directory
     */
    public function directory($directory, $flags = Directory::IGNORE_VCS)
    {
        return new Directory($this, $directory, $flags);
    }

    /**
     * setContents
     *
     * @param mixed $file
     * @param mixed $content
     * @param mixed $writeFlags
     *
     * @return mixed
     */
    public function setContents($file, $content, $writeFlags = LOCK_EX)
    {
        return null === $writeFlags ?
            file_put_contents($file, $content) :
            file_put_contents($file, $content, $writeFlags);
    }

    /**
     * getContents
     *
     * @param mixed $file
     * @param mixed $readFlags
     *
     * @return mixed
     */
    public function getContents($file, $includepath = null, $context = null, $start = null, $stop = null)
    {
        return null === $start ?
            file_get_contents($file, $includepath, $context) :
            file_get_contents($file, $includepath, $context, $start, $stop);
    }

    /**
     *
     * @param mixed $destination
     * @param mixed $content
     *
     * @return Boolean
     */
    public function writeStream($destination, $content)
    {
        if ($stream !== fopen($destination, 'w+')) {
            return false;
        }

        while (!feof($content)) {
            fwrite($stream, fread($content, 1024), 1024);
        }

        if (!fclose($stream)) {
            return false;
        }

        return true;
    }

    /**
     * move
     *
     * @param mixed $param
     *
     * @return mixed
     */
    public function rename($source, $target, $overwrite = false)
    {
        if (!$overwrite && $this->exists($target)) {
            throw new IOException(sprintf('Use overwrite to rename %s to existing file %s', $source, $target));
        }
        if (true !== @rename($source, $target)) {
            throw new IOException(sprintf('could no rename %s to %s', $source, $target));
        }
    }

    /**
     * setCopyPrefix
     *
     * @param mixed $prefix
     *
     * @return string
     */
    public function setCopyPrefix($prefix)
    {
        $this->copyPrefix = $prefix;
    }

    /**
     * setCopyStartOffset
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function setCopyStartOffset($offset)
    {
        $this->copyStartOffset = $offset;
    }

    /**
     * getCopyPrefix
     *
     * @return string
     */
    public function getCopyPrefix()
    {
        if (null === $this->copyPrefix) {
            return static::COPY_PREFIX;
        }

        return $this->copyPrefix;
    }

    /**
     * getCopyStartOffset
     *
     *
     * @return string
     */
    public function getCopyStartOffset()
    {
        if (null === $this->copyStartOffset) {
            return static::COPY_START_OFFSET;
        }

        return $this->copyStartOffset;
    }

    public function getExtension($file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * Enumerate a directory or file name based on its occurrance of the parent
     * directory.
     *
     * @param string $file    the paht of the file or directory.
     * @param integer $start  the enumeration base.
     * @param string  $prefix the enumeration static prefix, e.g. "copy".
     * @param boolean $pad    there's a prefix and paddin is true, it will add.
     *
     * @return string
     */
    public function enum($file, $start = 0, $prefix = null, $pad = true)
    {
        if ($this->isFile($file)) {
            return $this->enumFile($file, $start, $prefix, $pad);
        }

        if ($this->isDir($file)) {
            return $this->enumDir($file, $start, $prefix, $pad);
        }
    }

    /**
     * backup
     *
     * @param string $file
     * @param string $dateFormat
     * @param string $suffix
     *
     * @return void
     */
    public function backup($file, $dateFormat = 'Y-m-d-His', $suffix = '~')
    {
        $date = (new \DateTime())->format('Y-m-d-His');
        $target = null;

        if ($this->isFile($file)) {
            $target = $this->getBackupFileName($file.$suffix, $date);
        } elseif ($this->isDir($file)) {
            $target =  $this->getBackupDirName($file, $date, $suffix);
        }

        if (null !== $target) {
            $this->copy($file, $target);
        }
    }

    /**
     * rmRecursive
     *
     * @param mixed $dir
     *
     * @return mixed
     */
    protected function rmRecursive($dir)
    {
        $iterator = new FilesystemIterator($dir, FilesystemIterator::CURRENT_AS_SELF|FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $fileInfo) {

            if ($fileInfo->isFile() || $fileInfo->isLink()) {
                $this->unlink($fileInfo->getPathName());
                continue;
            }

            if ($fileInfo->isDir()) {
                $this->rmRecursive($dir = $fileInfo->getPathName());
                rmdir($dir);
                continue;
            }
        }
    }

    /**
     * getBackupFileName
     *
     * @param string $source
     * @param string $date
     *
     * @return string
     */
    protected function getBackupFileName($source, $date)
    {
        $sp        = $this->getSeparator();
        $num       = 1;
        $extension = pathinfo($source, PATHINFO_EXTENSION);
        $basename  = basename($source, $extension);
        $basename  = dirname($source).$sp.$basename.$date;
        $file      = $basename.'.'.$extension;

        while ($this->isFile($file)) {
            $file = $basename . '-' . (string)$num++ .'.'.$extension;
        }

        return $file;
    }

    /**
     * getBackupDirName
     *
     * @param string $source
     * @param string $date
     * @param string $suffix
     *
     * @return string
     */
    protected function getBackupDirName($source, $date, $suffix)
    {
        $num     = 1;
        $dirname = $source . '-' .$date;
        $dir     = $dirname.$suffix;

        while ($this->isDir($dir)) {
            $dir = $dirname . '-' . (string)$num++.$suffix;
        }

        return $dir;
    }

    /**
     * Enumerate a directory name based on its occurrance of the parent
     * directory.
     *
     * @see \Selene\Module\Filesystem::enum()
     *
     * @param string  $dir      the path of directory.
     * @param integer $counter  the enumeration count offset.
     * @param string  $prefix   the enumeration name prefix.
     * @param boolean $prefix   if true, prefix will be padded with spaces.
     *
     * @return string
     */
    protected function enumDir($dir, $counter, $prefix = null, $pad = true)
    {
        $prefix = null === $prefix ?
            $prefix :
            ($pad ? str_pad($prefix, strlen($prefix) + 2, ' ', STR_PAD_BOTH) : $prefix);

        while (is_dir($target = sprintf("%s%s%d", $dir, $prefix, $counter))) {
            $counter++;
        }
        return $target;
    }

    /**
     * Enumerate a file name based on its occurrance of the parent
     * directory.
     *
     * @see \Selene\Module\Filesystem::enum()
     *
     * @param string $file the path of file.
     * @param integer $counter  the enumeration count offset.
     * @param string  $prefix   the enumeration name prefix.
     * @param boolean $prefix   if true, prefix will be padded with spaces.
     *
     * @return string
     */
    protected function enumFile($file, $counter, $prefix = null, $pad = true)
    {
        $prefix = null === $prefix ?
            $prefix :
            ($pad ? str_pad($prefix, strlen($prefix) + 2, ' ', STR_PAD_BOTH) : $prefix);

        $path      = dirname($file).$this->getSeparator();
        $extension = 0 < strlen($ext = pathinfo($file, PATHINFO_EXTENSION)) ? ".$ext" : '';
        $basename = basename($file, $extension);

        $counte = $counter;

        while (file_exists($target = sprintf("%s%s%s%d%s", $path, $basename, $prefix, $counter, $extension))) {
            $counter++;
        }

        return $target;
    }

    /**
     * getIterator
     *
     * @param mixed $file
     * @param mixed $flags
     *
     * @return FilesystemIterator
     */
    protected function getIterator($file, $flags = FilesystemIterator::CURRENT_AS_SELF)
    {
        return new FilesystemIterator($file, FilesystemIterator::SKIP_DOTS|$flags);
    }

    /**
     * doCopyFile
     *
     * @param mixed $source
     * @param mixed $target
     *
     * @access protected
     * @return integer
     */
    protected function doCopyFile($source, $target)
    {
        $size   = filesize($source);
        $source = fopen($source, 'r');
        $target = fopen($target, 'w');

        $bytes = stream_copy_to_stream($source, $target, $size, 0);

        fclose($source);
        fclose($target);

        return $bytes;
    }

    /**
     * doCopyDir
     *
     * @TODO: integrate symlink copying.
     *
     * @param string $source
     * @param string $target
     * @param integer $flags
     *
     * @return integer
     */
    protected function doCopyDir($source, $target, $flags)
    {
        $this->ensureDirectory($target);

        $bytes = 0;
        $sp = $this->getSeparator();

        foreach (new FilesystemIterator($source, $flags) as $path => $info) {
            $tfile = $target . $sp . $info->getBaseName();

            if ($info->isDir()) {
                $bytes += $this->doCopyDir($path, $tfile, $flags);
            }

            if ($info->isFile()) {
                $bytes += $this->doCopyFile($path, $tfile);
            }
        }

        return $bytes;
    }

    /**
     * getSeparator
     *
     * @return string
     */
    protected function getSeparator()
    {
        return DIRECTORY_SEPARATOR;
    }

    /**
     * validateCopyTarget
     *
     * @param string $target
     * @param boolean $replace
     *
     * @throws IOException
     * @return string
     */
    protected function validateCopyTarget($source, $target = null, $replace = false)
    {
        if (null === $target) {
            $target = $this->enum($source, $this->getCopyStartOffset(), $this->getCopyPrefix());
        } elseif ($this->exists($target)) {
            if (!$replace) {
                throw new IOException(sprintf('target %s exists', $target));
            }
            $this->remove($target);
        }

        return $target;
    }

    /**
     * ensureTraversable
     *
     * @param mixed $file
     *
     * @return Traversable
     */
    protected function ensureTraversable($file)
    {
        if (!(is_array($file) || $file instanceof \Traversable)) {
            return [$file];
        }

        return $file;
    }
}
