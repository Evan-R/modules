<?php

/**
 * This File is part of the Selene\Components\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

/**
 * @class Template implements TemplateInterface
 * @see TemplateInterface
 *
 * @package Selene\Components\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Template implements TemplateInterface
{
    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * engine
     *
     * @var string
     */
    protected $engine;

    /**
     * path
     *
     * @var string
     */
    protected $path;

    /**
     * @param string $name
     * @param string $engine
     *
     * @access public
     */
    public function __construct($name, $engine)
    {
        $this->name = $name;
        $this->engine = $engine;
    }

    /**
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->name . '.' . $this->engine;
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getEngine
     *
     * @access public
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * setPath
     *
     * @param mixed $path
     *
     * @access public
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * getPath
     *
     * @access public
     * @return void
     */
    public function getPath()
    {
        return $this->path;
    }
}
