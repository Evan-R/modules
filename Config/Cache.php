<?php

/*
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config;

use \Selene\Module\Filesystem\Filesystem;
use \Selene\Module\Filesystem\Traits\FsHelperTrait;
use \Selene\Module\Filesystem\Traits\PathHelperTrait;
use \Selene\Module\Filesystem\Exception\IOException;
use \Selene\Module\Config\Resource\Collector;

/**
 * @class Cache
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Cache implements CacheInterface
{
    use FsHelperTrait, PathHelperTrait {
        FsHelperTrait::mask as private getMask;
    }

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
     * fs
     *
     * @var \Selene\Module\Filesystem\Filesystem
     */
    private $fs;

    /**
     * @param mixed $file
     * @param Filesystem $files
     * @param mixed $debug
     *
     * @access public
     */
    public function __construct($file, $debug = true)
    {
        $this->file = $file;
        $this->debug = $debug;
        $this->resources = new Collector;
    }

    public function getResourceCollector()
    {
        return $this->resources;
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
    public function setFilesystem(Filesystem $files)
    {
        $this->fs = $files;
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
     * getFile
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * forget
     *
     * @return void
     */
    public function forget()
    {
        $this->getFs()->remove([$this->file, $this->file . '.manifest']);
    }

    /**
     * Check if the chache is still valid.
     *
     * @api
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
     * @return void
     */
    public function write($data, array $manifest = null)
    {
        $fs = $this->getFs();

        $fs->ensureFile($this->file);
        $fs->mask($this->file);
        $fs->setContents($this->file, $data);

        if (null !== $manifest) {
            $file = $this->getManifest();
            $fs->ensureFile($file);
            $fs->mask($file);
            $fs->setContents($file, serialize($manifest));
        }
    }

    /**
     * getManifest
     *
     * @access protected
     * @return string
     */
    protected function getManifest()
    {
        $ext = pathinfo($this->file, PATHINFO_EXTENSION);
        $manifest = substr($this->file, 0, -strlen($ext)).'manifest';

        return $manifest;
    }

    /**
     * validateManifest
     *
     * @return boolean
     */
    protected function validateManifest()
    {
        if (!is_file($manifest = $this->getManifest())) {
            return false;
        }

        $timestamp = filemtime($this->file);

        $this->resources->setResources(unserialize(file_get_contents($manifest)));

        return $this->resources->isValid($timestamp);
    }

    /**
     * getFs
     *
     * @access protected
     * @return Filesystem
     */
    protected function getFs()
    {
        return $this->fs ?: ($this->fs = new Filesystem);
    }
}
