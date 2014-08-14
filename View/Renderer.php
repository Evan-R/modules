<?php

/**
 * This File is part of the Selene\Module\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View;

use \Selene\Module\View\Template\EngineInterface;

/**
 * @class View
 * @package Selene\Module\View
 * @version $Id$
 */
class Renderer implements RendererInterface
{
    private $view;

    private $engine;

    private $template;

    private $context;

    /**
     * Constructor.
     *
     * @param DispatcherInterface $view
     * @param EngineInterface $engine
     * @param mixed $template
     * @param array $context
     */
    public function __construct(DispatcherInterface $view, EngineInterface $engine, $template, array $context)
    {
        $this->engine     = $engine;
        $this->context    = $context;
        $this->template   = $template;
        $this->dispatcher = $view;
    }

    /**
     * getViewDispatcher
     *
     * @return DispatcherInterface
     */
    public function getViewDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * getTemplate
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    public function getTemplateName()
    {
        return $this->template instanceof Template ? $this->template->getName() : $this->template;
    }

    /**
     * setContext
     *
     * @param mixed $context
     *
     * @return void
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * addContextValue
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function addContextValue($key, $value = null)
    {
        $this->mergeContext([(string)$key => $value]);
    }

    /**
     * addContext
     *
     * @param array $data
     *
     * @access public
     * @return mixed
     */
    public function addContext(array $data)
    {
        $this->mergeContext($data);
    }

    /**
     * nestRenderer
     *
     * @param mixed $key
     * @param mixed $template
     * @param array $context
     *
     * @return void
     */
    public function addRendererToContext($key, $template, array $context = [])
    {
        $this->addContext([(string)$key => $this->dispatcher->dispatch($template, $context)]);
    }

    protected function mergeContext(array $data)
    {
        $this->context = array_merge($this->context, $data);
    }

    /**
     * getContext
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * render
     *
     * @param array $context
     *
     * @return array
     */
    public function render()
    {
        $content = $this->engine->render($this->template, $this->context);

        return $content;
    }

    public function __toString()
    {
        return $this->render();
    }
}
