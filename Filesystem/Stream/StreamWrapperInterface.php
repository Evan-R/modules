<?php

/**
 * This File is part of the Selene\Module\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Filesystem\Stream;

/**
 * @interface StreamWrapperInterface
 *
 * @package Selene\Module\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
interface StreamWrapperInterface
{
    /**
     * Close directory handle.
     *
     * @return bool
     */
    public function dir_closedir();

    /**
     * Open directory handle.
     *
     * @param string $path
     * @param int $options
     *
     * @return bool
     */
    public function dir_opendir($path, $options);

    /**
     * Read entry from directory handle.
     *
     * @return string
     */
    public function dir_readdir();

    /**
     * Rewind directory handle.
     *
     * @return bool
     */
    public function dir_rewinddir();

    /**
     * mkdir
     *
     * @param string $path
     * @param int    $mode
     * @param int    $options
     *
     * @return bool
     */
    public function mkdir($path, $mode, $options);

    /**
     * rename
     *
     * @param string $path_from
     * @param string $path_to
     *
     * @return bool
     */
    public function rename($path_from, $path_to);

    /**
     * rmdir
     *
     * @param string $path
     * @param int $options
     *
     * @return bool
     */
    public function rmdir($path, $options);

    /**
     * stream_cast
     *
     * @param int $cast_as
     *
     * @return resource|bool false
     */
    public function stream_cast($cast_as);

    /**
     * stream_close
     *
     * @return void
     */
    public function stream_close();

    /**
     * stream_eof
     *
     * @return bool
     */
    public function stream_eof();

    /**
     * stream_flush
     *
     * @return bool
     */
    public function stream_flush();

    /**
     * stream_lock
     *
     * @param int $operation
     *
     * @return bool
     */
    public function stream_lock($operation);

    /**
     * stream_metadata
     *
     * @param string $oath
     * @param int $options
     * @param mixed $value
     *
     * @return bool
     */
    //public function stream_metadata($path, $option, $value);

    /**
     * stream_open
     *
     * @param string $path
     * @param string $mode
     * @param int    $options
     * @param string $opened_path
     *
     * @return bool
     */
    public function stream_open($path, $mode, $options, &$opened_path);

    /**
     * stream_read
     *
     * @param int $count
     *
     * @return string
     */
    public function stream_read($count);

    /**
     * stream_seek
     *
     * @param int $offset
     * @param int $whence
     *
     * @return bool
     */
    public function stream_seek($offset, $whence = SEEK_SET);

    /**
     * Change stream options.
     *
     * @param int $option
     * @param int $arg1
     * @param int $arg2
     *
     * @return bool
     */
    //public function stream_set_option($option, $arg1, $arg2);

    /**
     * stream_stat
     *
     * @return array
     */
    public function stream_stat();

    /**
     * stream_tell
     *
     * @return int
     */
    public function stream_tell();

    /**
     * Truncate stream.
     *
     * @param int $new_size
     *
     * @return bool
     */
    public function stream_truncate($new_size);

    /**
     * stream_write
     *
     * @param string $data
     *
     * @return int
     */
    public function stream_write($data);

    /**
     * unlink
     *
     * @param string $path
     *
     * @return bool
     */
    public function unlink($path);

    /**
     * url_stat
     *
     * @param string $path
     * @param int $flags
     *
     * @return array
     */
    public function url_stat($path, $flags);
}
