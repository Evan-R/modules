<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config;

use \Selene\Components\Filesystem\Filesystem;
use \Selene\Components\Filesystem\Traits\FsHelperTrait;
use \Selene\Components\Filesystem\Traits\PathHelperTrait;
use \Selene\Components\Filesystem\Exception\IOException;

/**
 * @class Cache
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Cache
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
    public function __construct($file, $debug = true)
    {
        $this->file = $file;
        $this->debug = $debug;
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
        $this->files = $files;
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
        $fs = $this->getFs();

        $fs->mask($this->file);
        $fs->setContents($this->file, $content);

        if (null !== $manifest) {
            $fs->mask($file = $this->file.'.manifest');
            $fs->setContents($file, serialize($manifest));
        }
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

    /**
     * getFs
     *
     * @access protected
     * @return \Selene\Components\Filesystem\Filesystem
     */
    protected function getFs()
    {
        return $this->fs ?: ($this->fs = new Filesystem);
    }
}
