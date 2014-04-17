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

use FilesystemIterator;
use \Selene\Components\Common\Traits\PathHelper;
use Selene\Components\Filesystem\Exception\IOException;

/**
 * @class Filesystem
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Filesystem
{
    use PathHelper;

    /**
     * @var string
     */
    const COPY_PREFIX = 'copy';

    /**
     * @var int
     */
    const COPY_START_OFFSET = 1;

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
     * @access public
     */
    public function __construct($directoryPermissions = 0755, $filePermissions = 0644)
    {
        $this->directoryPermissions  = $directoryPermissions;
        $this->filePermissions       = $filePermissions;
    }

    /**
     * create
     *
     * @access public
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
     * @access public
     * @return void
     */
    public function mkdir($dir, $permissions = 0755, $recursive = true)
    {
        try {
            mkdir($dir, $permissions, $recursive);
        } catch (\Exception $e) {
            throw new IOException($e->getMessage());
        }
    }

    /**
     * Removes a directory.
     *
     * @param string $dir the directory path.
     *
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @access public
     * @return void
     */
    public function rmdir($directory)
    {
        try {
            $this->rmRecursive($directory);
            rmdir($directory);
        } catch (\Exception $e) {
            throw new IOException(sprintf('could not remove directory %s', $directory));
        }
    }

    /**
     * Removes all conatining files and directories within the given directory.
     *
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @access public
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
     * @access public
     * @return bool
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
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @access public
     * @return boolean true if touching of the file was successful.
     */
    public function touch($file, $time = null, $atime = null)
    {
        $touched = $time ? @touch($file, $time, $atime) : @touch($file);

        if (true !== $touched) {
            throw new IOException(sprintf('could not touch file %s', $file));
        }

        return $touched;
    }

    /**
     * Deletes a file.
     *
     * @param string $file the file path.
     *
     * @access public
     * @return mixed
     */
    public function unlink($file)
    {
        return unlink($file);
    }

    /**
     * remove
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function remove($file)
    {
        if ($this->isFile($file)) {
            return $this->unlink($file);
        }
        if ($this->isDir($file)) {
            return $this->rmdir($file);
        }
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
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @access public
     * @return integer returns the total count of bytes that where copied.
     */
    public function copy($source, $target = null, $replace = false)
    {
        if (!$this->isFile($source) && !$this->isDir($source)) {
            throw new IOException(sprintf('%s: no such file or directory', $source));
        }

        if (is_null($target)) {
            $target = $this->enum($source, $this->getCopyStartOffset(), $this->getCopyPrefix());
        } elseif ($this->exists($target)) {
            if (!$replace) {
                throw new IOException(sprintf('target %s exists', $target));
            }
            $this->remove($target);
        }

        $this->ensureDirectory(dirname($target));

        $source = fopen($source, 'r');
        $target = fopen($target, 'w+');
        $result = stream_copy_to_stream($source, $target);

        fclose($source);
        fclose($target);

        return $result;
    }

    /**
     * Set file permissions.
     *
     * @param string  $file
     * @param integer $permission
     * @param boolean $recursive
     *
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @access public
     * @return void
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
                throw new IOException('filerpermissions could not be set');
            }
        }
    }

    /**
     * chown
     *
     * @param mixed $param
     *
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @access public
     * @return mixed
     */
    public function chown($file, $owner, $recursive = true)
    {
        if (!$this->uidExists($owner)) {
            throw new IOException(sprintf('group %s does not exist', $owner));
        }

        foreach ($this->ensureTraversable($file) as $item) {
            if ($this->isLink($item) && function_exists('lchown')) {
                if (true !== @lchown($item, $owner)) {
                    throw new IOException(sprintf('could not change owner on link %s', $item));
                }
                continue;
            }

            if ($recursive && !$this->isLink($item) && $this->isDir($item)) {
                $this->chown($this->getIterator($item, FilesystemIterator::CURRENT_AS_PATHNAME), $owner, true);
            }

            if (true !== @chown($item, $owner)) {
                throw new IOException(sprintf('could not change owner on %s', $item));
            }
        }
    }

    /**
     * chgrp
     *
     * @param mixed $file
     * @param mixed $group
     *
     * @access public
     * @return void
     */
    public function chgrp($file, $group, $recursive = true)
    {
        if (!$this->gidExists($group)) {
            throw new IOException(sprintf('group %s does not exist', $group));
        }

        foreach ($this->ensureTraversable($file) as $item) {
            if ($this->isLink($item) && function_exists('lchgrp')) {
                if (true !== @lchgrp($item, $owner)) {
                    throw new IOException(sprintf('could not change group on link %s', $item));
                }
                continue;
            }

            if ($recursive && !$this->isLink($item) && $this->isDir($item)) {
                $this->chgrp($this->getIterator($item, FilesystemIterator::CURRENT_AS_PATHNAME), $group, true);
            }

            if (true !== @chgrp($item, $group)) {
                throw new IOException(sprintf('could not change group on %s', $item));
            }
        }
    }

    /**
     * mask
     *
     * @param mixed $file
     * @param mixed $mode
     *
     * @access public
     * @return mixed
     */
    public function mask($file, $mode = null)
    {
        $cmask = $mode ?: ($this->isFile($file) ? 0666 : ($this->isDir($file) ? 0775 : $mode));

        if (null === $cmask) {
            throw new \InvalidArgumentException(sprintf('%s is not a file or a directory', $file));
        }

        return $this->chmod($file, $this->getMask($cmask));
    }

    /**
     * getMask
     *
     * @param mixed $cmask
     *
     * @access public
     * @return integer
     */
    public function getMask($cmask)
    {
        return $cmask & ~umask();
    }

    /**
     * exists
     *
     * @param mixed $param
     *
     * @access public
     * @return bool
     */
    public function exists($file)
    {
        return $this->isDir($file) or $this->isFile($file);
    }

    /**
     * isDir
     *
     * @param mixed $file
     *
     * @access public
     * @return bool
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
     * @access public
     * @return bool
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
     * @access public
     * @return bool
     */
    public function isLink($file)
    {
        return stream_is_local($file) && is_link($file);
    }

    /**
     * ensure
     *
     * @param mixed $file
     *
     * @access public
     * @return void
     */
    public function ensureDirectory($dir)
    {
        if (!$this->isDir($dir)) {
            $this->mkdir($dir, $this->directoryPermissions, true);
        }
    }

    /**
     * ensureFile
     *
     * @param mixed $file
     *
     * @access public
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
     * userExists
     *
     * @param mixed $uid
     *
     * @access public
     * @return bool
     */
    public function uidExists($uid)
    {
        if (is_numeric($uid) && false !== @posix_getpwuid((int)$uid)) {
            return true;
        }

        return false !== @posix_getpwnam($uid);
    }

    /**
     * groupExists
     *
     * @param mixed $groupID
     *
     * @access public
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
     * fileMTime
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function fileMTime($file)
    {
        return filemtime($file);
    }

    /**
     * fileATime
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function fileATime($file)
    {
        return fileatime($file);
    }

    /**
     * fileCTime
     *
     * @param mixed $file
     *
     * @access public
     * @return mixed
     */
    public function fileCTime($file)
    {
        return filectime($file);
    }

    /**
     * file
     *
     * @param mixed $file
     *
     * @access public
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
     * @access public
     * @return Directory
     */
    public function directory($directory, $flags = Directory::IGNORE_VCS)
    {
        return new Directory($this, $directory, $flags);
    }

    /**
     * rmRecursive
     *
     * @param mixed $dir
     *
     * @access protected
     * @return mixed
     */
    protected function rmRecursive($dir)
    {
        $iterator = new FilesystemIterator($dir, FilesystemIterator::CURRENT_AS_SELF|FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $fileInfo) {

            if ($fileInfo->isFile() or $fileInfo->isLink()) {
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
     * setContents
     *
     * @param mixed $file
     * @param mixed $content
     * @param mixed $writeFlags
     *
     * @access public
     * @return mixed
     */
    public function setContents($file, $content, $writeFlags = LOCK_EX)
    {
        return is_null($writeFlags) ?
            file_put_contents($file, $content) :
            file_put_contents($file, $content, $writeFlags);
    }

    /**
     * getContents
     *
     * @param mixed $file
     * @param mixed $readFlags
     *
     * @access public
     * @return mixed
     */
    public function getContents($file, $includepath = null, $context = null, $start = null, $stop = null)
    {
        return is_null($start) ?
            file_get_contents($file, $includepath, $context) :
            file_get_contents($file, $includepath, $context, $start, $stop);
    }

    /**
     * move
     *
     * @param mixed $param
     *
     * @access public
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
     * @access public
     * @return string
     */
    public function setCopyPrefix($prefix)
    {
        $this->copyPrefix = $prefix;
    }

    public function setCopyStartOffset($offset)
    {
        $this->copyStartOffset = $offset;
    }

    /**
     * getCopyPrefix
     *
     * @access protected
     * @return string
     */
    public function getCopyPrefix()
    {
        if (is_null($this->copyPrefix)) {
            return static::COPY_PREFIX;
        }

        return $this->copyPrefix;
    }

    /**
     * getCopyStartOffset
     *
     *
     * @access public
     * @return string
     */
    public function getCopyStartOffset()
    {
        if (is_null($this->copyStartOffset)) {
            return static::COPY_START_OFFSET;
        }

        return $this->copyStartOffset;
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
     * @access public
     * @return string
     */
    public function enum($file, $start, $prefix = null, $pad = true)
    {
        if ($this->isFile($file)) {
            return $this->enumFile($file, $start, $prefix, $pad);
        }

        if ($this->isDir($file)) {
            return $this->enumDir($file, $start, $prefix, $pad);
        }
    }

    /**
     * Enumerate a directory name based on its occurrance of the parent
     * directory.
     *
     * @param string $dir the path of directory.
     * @see \Selene\Components\Filesystem::enum()
     * @access protected
     * @return string
     */
    protected function enumDir($dir, $start, $prefix = null, $pad = true)
    {
        $prefix = is_null($prefix) ?
            $prefix :
            ($pad ? str_pad($prefix, strlen($prefix) + 2, ' ', STR_PAD_BOTH) : $prefix);
        $i = $start;
        while (is_dir(sprintf("%s%s%d", $dir, $prefix, $i))) {
            $i++;
        }
        return sprintf("%s%s%d", $dir, $prefix, $i);
    }

    /**
     * Enumerate a file name based on its occurrance of the parent
     * directory.
     *
     * @param string $file the path of file.
     * @see \Selene\Components\Filesystem::enum()
     *
     * @access protected
     * @return string
     */
    protected function enumFile($file, $start, $prefix = null, $pad = true)
    {
        $prefix = is_null($prefix) ?
            $prefix :
            ($pad ? str_pad($prefix, strlen($prefix) + 2, ' ', STR_PAD_BOTH) : $prefix);

        $name      = pathinfo($file, PATHINFO_FILENAME);
        $path      = dirname($file).DIRECTORY_SEPARATOR;
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $extension = strlen($extension) ? ".$extension" : '';

        $i = $start;

        while (file_exists(sprintf("%s%s%s%d%s", $path, $name, $prefix, $i, $extension))) {
            $i++;
        }
        return sprintf("%s%s%s%d%s", $path, $name, $prefix, $i, $extension);
    }

    /**
     * getIterator
     *
     * @param mixed $file
     * @param mixed $flags
     *
     * @access protected
     * @return FilesystemIterator
     */
    protected function getIterator($file, $flags = FilesystemIterator::CURRENT_AS_SELF)
    {
        return new FilesystemIterator($file, FilesystemIterator::SKIP_DOTS|$flags);
    }

    /**
     * ensureTraversable
     *
     * @param mixed $file
     *
     * @access private
     * @return Traversable
     */
    private function ensureTraversable($file)
    {
        if (!(is_array($file) or $file instanceof \Traversable)) {
            return [$file];
        }
        return $file;
    }
}
