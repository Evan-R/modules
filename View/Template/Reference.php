<?php

/**
 * This File is part of the Selene\Module\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Template;

/**
 * @class Reference
 * @package Selene\Module\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Reference implements ReferenceInterface
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $engine
     */
    public function __construct($name = null, $engine = null)
    {
        $this->name = $name;
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * setName
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setPath
     *
     * @param mixed $path
     *
     * @access public
     * @return mixed
     */
    public function setPath($path)
    {
        $this->name = $path;
    }

    /**
     * getPath
     *
     * @return string
     */
    public function getPath()
    {
        return $this->name;
    }

    /**
     * setEngine
     *
     * @param mixed $path
     *
     * @access public
     * @return mixed
     */
    public function setEngine($path)
    {
        $this->engine;
    }

    /**
     * getEngine
     *
     *
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }
}
