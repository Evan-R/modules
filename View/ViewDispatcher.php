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

use \SplStack;
use \Selene\Module\View\Template\EngineInterface;
use \Selene\Module\View\Template\EngineResolverInterface;
use \Selene\Module\View\Template\LoaderInterface;
use \Selene\Module\View\Composer\ComposerInterface;
use \Selene\Module\View\Exception\RenderException;

/**
 * @class ViewManager implements ManagerInterface
 * @see ManagerInterface
 *
 * @package Selene\Module\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ViewDispatcher implements DispatcherInterface
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
    private $pool;

    /**
     * Constructor.
     *
     * @param EngineResolverInterface $engines
     * @param ComposerInterface $composer
     */
    public function __construct(EngineResolverInterface $engines, ComposerInterface $composer = null)
    {
        $this->engines  = $engines;
        $this->composer = $composer;

        $this->pool = [];
        $this->contexts = [];
    }

    /**
     * dispatch
     *
     * @param string $template
     * @param array  $context
     * @param array  $merge
     *
     * @return RendererInterface
     */
    public function dispatch($template, array $context = [], array $merge = [])
    {
        if (null === ($engine = $this->findEngineByTemplate($template))) {
            return;
        }

        $context = empty($merge) ? $context : array_merge($merge, $context);

        $this->notifyComposers($renderer = new Renderer($this, $engine, $template, $context));

        return $renderer->render();
    }

    /**
     * render
     *
     * @param mixed $template
     * @param array $context
     *
     * @return RendererInterface
     */
    public function render($template, array $context = [], array $merge = [])
    {
        return $this->dispatch($template, $context);
    }

    /**
     * notifyComposers
     *
     * @param RendererInterface $renderer
     *
     * @access public
     * @return mixed
     */
    public function notifyComposers(RendererInterface $renderer)
    {
        $this->composer->compose($renderer);
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
        $contents = '';

        do {
            $contents .= $this->contents->shift();
        } while ($this->contents->valid());

        return $contents;
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
        var_dump('add context');
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
        var_dump('flush context');
        var_dump($this->contexts);
        $contexts = $this->contexts;

        array_unshift($contexts, $renderContext);

        $context = call_user_func_array('array_merge', $contexts);

        $this->contexts = [];

        return $context;
    }

    /**
     * callComposer
     *
     * @param string $template
     * @param array|null $context
     *
     * @return void
     */
    protected function callComposer($template, $context)
    {
        $this->contents->push($this->composer->render($this, $template, $context));
    }

    /**
     * findTemplate
     *
     * @param string $name
     *
     * @return EngineInterface
     */
    public function findEngineByName($name)
    {
        if (null === ($engine = $this->engines->resolve($name))) {
            return;
        }

        $engine->setView($this);

        return $engine;
    }

    /**
     * findEngine
     *
     * @param mixed $template
     *
     * @throws RenderException if no engin is found.
     * @return EngineInterface
     */
    public function findEngineByTemplate($template)
    {
        $engine = null;
        $name = $template instanceof Template ? $template->getName() : $template;

        if (isset($this->pool[$name])) {
            $engine = $this->engines->resolve($this->pool[$name]);
        } elseif ($engine = $this->engines->resolveByName($name)) {
            $this->pool[$name] = $engine->getType();
        }

        if (null !== $engine) {

            $engine->setView($this);

            return $engine;
        }

        throw new RenderException(
            sprintf('No suitable template engine found for template "%s".', htmlspecialchars($template))
        );
    }
}
