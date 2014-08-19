<?php

/*
 * This File is part of the \Users\malcolm\www\selene_source\src\Selene\Module\Routing\Cache package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Cache;

use \Selene\Module\Filesystem\Traits\FsHelperTrait;

/**
 * @class FilesystemDriver
 * @package Selene\Module\Routing\Cache
 * @version $Id$
 */
class FilesystemDriver implements DriverInterface
{
    use FsHelperTrait;

    private $path;

    public function __construct($path = null)
    {
        $this->path = $path;
    }

    /**
     * put
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @access public
     * @return void
     */
    public function put($id, $content)
    {
        $this->putContent($this->getFile($id), $content);
    }

    /**
     * replace
     *
     * @param mixed $id
     * @param mixed $content
     *
     * @access public
     * @return void
     */
    public function replace($id, $content)
    {
        return $this->put($id, $content);
    }

    /**
     * remove
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function remove($id)
    {
        if ($this->has($id)) {
            $file = $this->getFile($id);
            return @unlink($file);
        }

        return false;
    }

    public function get($id)
    {
        return $this->getContent($this->getFile($id));
    }

    /**
     * has
     *
     * @param mixed $id
     *
     * @access public
     * @return boolean
     */
    public function has($id)
    {
        return file_exists($this->getFile($id));
    }

    /**
     * getContent
     *
     * @param mixed $file
     *
     * @access private
     * @return mixed
     */
    private function getContent($file)
    {
        return unserialize(file_get_contents($file));
    }

    /**
     * putContent
     *
     * @param mixed $file
     * @param mixed $content
     *
     * @access private
     * @return boolean
     */
    private function putContent($file, $content)
    {
        $this->ensureExists(dirname($file));
        touch($file);

        return file_put_contents($file, serialize($content));
    }

    private function ensureExists($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    /**
     * getFile
     *
     * @param mixed $id
     *
     * @access private
     * @return string
     */
    private function getFile($id)
    {
        return $this->path . DIRECTORY_SEPARATOR . $id;
    }
}
