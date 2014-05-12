<?php

/**
 * This File is part of the Selene\Components\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View;

use \Selene\Components\View\Template\EngineInterface;
use \Selene\Components\View\Template\LoaderInterface;

/**
 * @class ViewManager implements ManagerInterface
 * @see ManagerInterface
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ViewManager implements ManagerInterface
{
    /**
     * locator
     *
     * @var mixed
     */
    private $locator;

    /**
     * engines
     *
     * @var array
     */
    private $engines;

    /**
     * @param mixed $engines
     *
     * @access public
     * @return mixed
     */
    public function __construct(LoaderInterface $loader, array $engines = [])
    {
        $this->loader = $loader;
        $this->engines = [];
        $this->registerEngines($engines);
    }

    /**
     * render
     *
     * @param mixed $template
     * @param array $context
     *
     * @access public
     * @return mixed
     */
    public function render($template, array $context = [])
    {
        if (!$engine = $this->findEngine($template)) {
            throw new \RuntimeException(sprintf('no suitable template engine found for %s', $template));
        }

        return $engine->render($this->loader->load($template), $context);
    }

    /**
     * findTemplate
     *
     * @param mixed $template
     *
     * @access protected
     * @return mixed
     */
    protected function findTemplate($template)
    {
        $file = $this->locator->locate($template);
        return $file;
    }

    /**
     * registerEngines
     *
     * @param array $engines
     *
     * @access public
     * @return void
     */
    public function registerEngines(array $engines)
    {
        foreach ($engines as $engine) {
            $this->registerEngine($engine);
        }
    }

    /**
     * registerEngine
     *
     * @param EngineInterface $engine
     *
     * @access public
     * @return void
     */
    public function registerEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }

    /**
     * findEngine
     *
     * @param mixed $template
     *
     * @access public
     * @return EngineInterface
     */
    public function findEngine($template)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($template)) {
                return $engine;
            }
        }

        return false;
    }
}
