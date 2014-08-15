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
 * @abstract class StreamWrapper implements StreamWrapperInterface
 * @see StreamWrapperInterface
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class StreamWrapper implements StreamWrapperInterface
{
    /**
     * @var resource
     */
    protected $handle;

    /**
     * {@inheritdoc}
     */
    public function dir_closedir()
    {
        closedir($this->handle);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function dir_opendir($uri, $options)
    {
        return (bool)$this->handle = opendir($this->getTarget($uri));
    }

    /**
     * {@inheritdoc}
     */
    public function dir_readdir()
    {
        return readdir($this->handle);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_open($uri, $mode, $options, &$opened_url)
    {
        return (bool)$this->handle = fopen($this->getTarget($uri), $mode);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_close()
    {
        fclose($this->handle);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_lock($operation)
    {
        if (in_array($operation, [LOCK_SH, LOCK_EX, LOCK_UN, LOCK_NB])) {
            return flock($this->handle, $operation);
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function stream_read($count)
    {
        return fread($this->handle, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_write($data)
    {
        return fwrite($this->handle, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_eof()
    {
        return feof($this->handle);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_seek($offset, $whence = 0)
    {
        return !fseek($this->handle, $offset, $whence);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_flush()
    {
        return fflush($this->handle);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_tell()
    {
        return ftell($this->handle);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_stat()
    {
        return fstat($this->handle);
    }

    /**
     * @param $uri string
     * @return bool
     */
    public function unlink($uri)
    {
        return unlink($this->getTarget($uri));
    }

    /**
     * {@inheritdoc}
     */
    public function stream_truncate($new_size)
    {
        return ftruncate($this->handle, $new_size);
    }

    /**
     * {@inheritdoc}
     */
    public function stream_cast($cast_as)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function rename($olduri, $newuri)
    {
        return rename($this->getTarget($olduri), $this->getTarget($newuri));
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($uri, $mode, $options)
    {
        return mkdir($this->getTarget($uri), $mode, $options & STREAM_MKDIR_RECURSIVE);
    }

    /**
     * {@inheritdoc}
     */
    public function rmdir($uri, $options)
    {
        return rmdir($this->getTarget($uri));
    }

    /**
     * {@inheritdoc}
     */
    public function url_stat($uri, $flags)
    {
        $path = $this->getTarget($uri);

        if ($flags & STREAM_URL_STAT_QUIET || !file_exists($path)) {
            return @stat($path);
        } else {
            return stat($path);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dir_rewinddir()
    {
        return rewinddir($this->handle);
    }

    /**
     * @return string
     */
    abstract protected function getTarget($uri);
}
