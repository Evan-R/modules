<?php

/*
 * This File is part of the Selene\Module\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

use \Selene\Module\Config\Cache;

/**
 * @class ContainerCache
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ContainerCache extends Cache
{
    private $valid;
    private $class;

    /**
     * Constructor.
     *
     * @param sring $class
     * @param sring $file
     * @param boolesn $debug
     */
    public function __construct($class, $file, $debug = false)
    {
        $this->valid = false;
        $this->class = $class;

        parent::__construct($file, $debug);
    }

    /**
     * isValid
     *
     * @return boolean
     */
    public function isValid()
    {
        if ($this->valid) {
            return true;
        }

        return $this->valid = parent::isValid();
    }

    public function setFile($file)
    {
        $this->valid = false;

        return parent::setFile();
    }

    /**
     * load
     *
     * @return ContainerInterface container
     */
    public function load()
    {
        try {
            return $this->doLoadClass();
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * doLoadClass
     *
     * @return ContainerInterface
     */
    private function doLoadClass()
    {
        if (!class_exists($class = $this->class)) {
            require $this->file;
        }

        return new $class;
    }
}
