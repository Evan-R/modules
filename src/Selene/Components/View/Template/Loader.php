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
 * @class Loader implements LoaderInterface
 * @see LoaderInterface
 *
 * @package Selene\Components\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Loader implements LoaderInterface
{
    /**
     * time
     *
     * @var mixed
     */
    protected $time;

    /**
     * locator
     *
     * @var mixed
     */
    protected $locator;

    /**
     * temlates
     *
     * @var mixed
     */
    protected $temlates;

    public function __construct(LocatorInterface $locator)
    {
        $this->time = time();
        $this->locator = $locator;
    }

    /**
     * load
     *
     * @access public
     * @return mixed
     */
    public function load($template)
    {
        return $this->get($template);
    }

    /**
     * isValid
     *
     * @param mixed $file
     *
     * @access public
     * @return boole
     */
    public function isValid($file)
    {
        return filemtime($file) <= $this->time;
    }

    /**
     * has
     *
     * @param mixed $template
     *
     * @access public
     * @return boolean
     */
    public function has($template)
    {
        return isset($this->templates[$template]) && $this->isValid($template);
    }

    /**
     * get
     *
     * @param mixed $template
     *
     * @access public
     * @return mixed
     */
    public function get($template)
    {
        if (!$this->has($template)) {
            $this->templates[$template] = $this->locator->locate($template);
        }

        return $this->templates[$template];
    }
}
