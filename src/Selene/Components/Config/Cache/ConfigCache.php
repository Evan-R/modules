<?php

/**
 * This File is part of the Selene\Components\Config\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Cache;

use \Selene\Components\Filesystem\Filesystem;
use \Selene\Components\Filesystem\Exception\IOException;

/**
 * @class ConfigCache
 *
 * @package Selene\Components\Config\Cache
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ConfigCache
{
    /**
     * debug
     *
     * @var boolean
     */
    protected $debug;

    /**
     * file
     *
     * @var string
     */
    protected $file;

    /**
     * files
     *
     * @var \Selene\Components\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @param mixed $file
     * @param Filesystem $files
     * @param mixed $debug
     *
     * @access public
     */
    public function __construct($file, Filesystem $files = null, $debug = true)
    {
        $this->file = $file;
        $this->debug = $debug;
        $this->setFilesystem($files);
    }

    /**
     * setDebug
     *
     * @param mixed $debug
     *
     * @debug
     * @access public
     * @return mixed
     */
    public function setDebug($debug)
    {
        $this->debug = (bool)$debug;
    }

    /**
     * setFilesystem
     *
     * @param Filesystem $files
     *
     * @access public
     * @return void
     */
    public function setFilesystem(Filesystem $files = null)
    {
        $this->files = $files ?: new Filesystem;
    }

    /**
     * setFile
     *
     * @param mixed $file
     *
     * @access public
     * @return void
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * forget
     *
     * @access public
     * @return mixed
     */
    public function forget()
    {
        $this->fs->remove([$this->file, $this->file . '.manifest']);
    }

    /**
     * Check if the chache is still valid.
     *
     * @api
     * @access public
     * @return boolean
     */
    public function isValid()
    {
        return !is_file($this->file) ? false : (!$this->debug ? true : $this->validateManifest());
    }

    /**
     * Write data to the cachefile.
     *
     * @param mixed $data
     * @param mixed $manifest
     *
     * @api
     * @access public
     * @return void
     */
    public function write($data, array $manifest = null)
    {
        $this->fs->mask($this->file);
        $this->fs->setContents($this->file, $content);

        if (null !== $manifest) {
            $this->fs->mask($file = $this->file.'.manifest');
            $this->fs->setContents($file, serialize($manifest));
        }
    }

    /**
     * Ensures that the cachedir is writable.
     *
     * @api
     * @throws \Selene\Components\Filesystem\Exception\IOException
     * @throws \RuntimeException
     * @access public
     * @return ConfigCache
     */
    public function ensureWritable()
    {
        if (!is_writable($dir = dirname($this->file))) {
            //try to set the directory to be writable.
            try {
                $this->fs->mask($dir);
            } catch (IOException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw new \RuntimeException(sprintf('trying to write to directory %s but it\'s not writable', $dir));
            }
        }

        return $this;
    }

    /**
     * validateManifest
     *
     *
     * @access protected
     * @return boolean
     */
    protected function validateManifest()
    {
        if (!is_file($manifest = $this->file.'.manifest')) {
            return false;
        }

        $timestamp = filemtime($this->file);

        foreach (unserialize(file_get_contents($manifest)) as $configFile) {
            if (!$configFile->isValid($timestamp)) {
                return false;
            }
        }

        return true;
    }
}
