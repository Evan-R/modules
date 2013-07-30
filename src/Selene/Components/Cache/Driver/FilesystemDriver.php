<?php

/**
 * This File is part of the Stream\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Cache\Driver;

use Selene\Components\Filesystem\Filesystem;

/**
 * DriverFileSystem
 *
 * @uses Storage
 * @package Stream\Cache
 * @version 1.0
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class FilesystemDriver extends AbstractDriver
{
    /**
     * Flag a file as compressed
     */
    const C_COMPRESSED = 1;

    /**
     *
     * Flag a file as uncompressed
     */
    const C_UNCOMPRESSED = 0;

    /**
     * cache directory
     *
     * @var Stream\FSDirectory
     * @access protected
     */
    protected $cachedir;

    /**
     * serializableObjects
     *
     * @var Mixed
     * @access protected
     */
    protected static $serializableObjects;

    /**
     * @param Filesystem $files
     * @param string $location
     *
     * @access public
     */
    public function __construct(Filesystem $files, $location)
    {
        $this->files    = $files;
        $this->cachedir = $this->files->directory($location);
        $this->setSerializer();
    }

    /**
     * cachedItemExists
     *
     * @param Mixed $cacheid
     * @access protected
     * @return void
     */
    public function cachedItemExists($key)
    {
        $file = $this->getFilePath($key);

        if (!$this->files->exists($file)) {
            return false;
        }

        $timestamp = $this->files->fileMTime($file);

        return time() < $timestamp;
    }

    /**
     * incrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access protected
     * @return bool
     */
    protected function incrementValue($key, $value)
    {
        return $this->setIncrementValue($key, $value);
    }

    /**
     * decrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access protected
     * @return boolean
     */
    protected function decrementValue($key, $value)
    {
        $value = 0 - $value;
        return $this->setIncrementValue($key, $value);
    }

    /**
     * setIncrementValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @access private
     * @return mixed
     */
    private function setIncrementValue($key, $value)
    {
        if ($this->cachedItemExists($key)) {
            $file = $this->getFilePath($key);
            extract($this->getFileContent($file));
            $data = $data + $value;
            $timestamp = $this->files->fileMTime($file);

            extract($this->serializeData($data, $state === static::C_UNCOMPRESSED));
            $this->files->setContents($file, $contents, LOCK_EX);
            $this->cachedir->touch($key, $timestamp);

            return true;
        }
        return false;
    }

    /**
     * getFromCache
     *
     * @param Mixed $key
     * @access protected
     * @return Mixed
     */
    public function getFromCache($key)
    {
        if (!$this->cachedItemExists($key)) {
            return;
        }
        extract($this->getFileContent($this->getFilePath($key)));
        return $data;
    }

    /**
     * getFileContent
     *
     * @param mixed $file
     *
     * @access protected
     * @return mixed
     */
    protected function getFileContent($file)
    {
        $contents = $this->files->getContents($file);
        $state = (int)substr($contents, 0, 1);
        $data = substr($contents, 2);

        if ($state === self::C_UNCOMPRESSED) {
            $data = unserialize($data);
        } else {
            $data = unserialize($this->uncompressData($data));
        }

        return compact('data', 'state');
    }


    /**
     * saveForever
     *
     * @param Mixed $cacheid
     * @param Mixed $data
     * @param Mixed $compressed
     * @access protected
     * @return Booelan
     */
    public function saveForever($key, $data, $compressed = false)
    {
        return $this->writeToCache($key, $data, "2037-12-31", $compressed);
    }

    /**
     * deleteFromCache
     *
     * @param Mixed $cacheid
     * @access protected
     * @return Boolean
     */
    public function deleteFromCache($key)
    {
        $this->files->remove($this->getFilePath($key));
        return true;
    }

    /**
     * flushCache
     *
     * @access protected
     * @return Boolean
     */
    public function flushCache()
    {
        $this->cachedir->flush();
        return true;
    }

    /**
     * writeToCache
     *
     * @todo test without igbinary
     *
     * @param Mixed $cacheid
     * @param Mixed $data
     * @param int $expires
     * @param Mixed $compressed
     * @access protected
     * @return Boolean
     */
    public function writeToCache($key, $data, $expires = 1, $compressed = false)
    {
        extract($this->serializeData($data, $compressed, $expires));

        $file = $this->getFilePath($key);
        $this->files->ensureFile($file);

        $this->files->setContents($file, $contents, LOCK_EX);
        $this->files->touch($file, $timestamp);
        return true;
    }

    /**
     * setSerializer
     *
     */
    protected function setSerializer()
    {
        if (is_null(static::$serializableObjects)) {
            static::$serializableObjects = $this->canSerializeObjects();
        }
    }

    /**
     * compressData
     *
     * @param Mixed $data
     * @access private
     * @return String base64 string representation of gzip compressed input
     * data
     */
    private function compressData($data)
    {
        return base64_encode(gzcompress($data));
    }

    /**
     * uncompressData
     *
     * @param Mixed $data
     * @access private
     * @return String Mixed contents of the cached item
     */
    private function uncompressData($data)
    {
        return gzuncompress(base64_decode($data));
    }

    private function getFilePath($key)
    {
        $hash = hash('md5', $key);
        return $this->cachedir->getRealPath(substr($hash, 0, 4).DIRECTORY_SEPARATOR.substr($hash, 4, 20));
    }

    /**
     * serializeWithTime
     *
     * @param Mixed $data
     * @param Mixed $time
     * @param Mixed $compressed
     * @access private
     * @return Mixed file contents
     */
    private function serializeData($data, $compressed = false, $time = null)
    {
        $timestamp = is_int($time) ?
            time() + ($time * 60) :
            (is_string($time) ?
                strtotime($time) :
                (time() + $this->default * 60)
            );

        $data = serialize($data);
        $data = $compressed ? $this->compressData($data) : $data;
        $contents = sprintf('%d;%s', $compressed ? self::C_COMPRESSED : self::C_UNCOMPRESSED, $data);

        return compact('contents', 'timestamp');
    }

    /**
     * canSerializeObjects
     *
     * @access private
     * @return Boolean
     */
    private function canSerializeObjects()
    {
        return extension_loaded('igbinary');
    }
}
