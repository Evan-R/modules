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
use \Selene\Components\View\Composer\ComposerInterface;
use \Selene\Components\View\Exception\RenderException;

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

    private $composer;

    private $contexts;

    /**
     * @param mixed $engines
     *
     * @access public
     * @return mixed
     */
    public function __construct(array $engines = [], ComposerInterface $composer = null)
    {
        $this->engines = [];
        $this->contexts = [];
        $this->contents = [];
        $this->registerEngines($engines);

        $this->composer = $composer;
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
        $this->withContext($context);

        if ($this->hasComposer($template)) {
            $this->callComposer($template, $context);
        } else {
            $this->doRender($template, $context);
        }

        return $this->flushContent();
    }

    /**
     * doRender
     *
     * @param mixed $template
     * @param array $context
     *
     * @return void
     */
    protected function doRender($template, $context)
    {
        $engine = $this->findEngine($template);

        $this->contents[] = $engine->render($template, $this->flushContext($context));
    }

    /**
     * hasComposer
     *
     * @param mixed $template
     *
     * @access protected
     * @return mixed
     */
    protected function hasComposer($template)
    {
        return null !== $this->composer && $this->composer->has($template);
    }

    /**
     * flushContent
     *
     *
     * @access public
     * @return mixed
     */
    public function flushContent()
    {
        $contents = $this->contents;

        $this->contents = [];

        return implode('', $contents);
    }

    /**
     * withContext
     *
     * @param array $context
     *
     * @access public
     * @return ManagerInterface
     */
    public function withContext(array $context)
    {
        $this->contexts[] = $context;

        return $this;
    }

    /**
     * flushContext
     *
     * @param array $renderContext
     *
     * @return array
     */
    protected function flushContext(array $renderContext)
    {
        $contexts = $this->contexts;

        array_unshift($contexts, $renderContext);

        $context = call_user_func_array('array_merge', $contexts);

        $this->contexts = [];

        return $context;
    }

    /**
     * callComposer
     *
     * @param mixed $template
     * @param mixed $context
     *
     * @access protected
     * @return null|string
     */
    protected function callComposer($template, $context)
    {
        $this->contents[] = $this->composer->render($this, $template, $context);
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
        return $template;
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
     * @throws RenderException if no engin is found.
     * @return EngineInterface
     */
    public function findEngine($template)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($template)) {
                return $engine;
            }
        }

        throw new RenderException(
            sprintf('No suitable template engine found for template "%s".', htmlspecialchars($template))
        );
    }
}
