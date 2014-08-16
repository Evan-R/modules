<?php

/**
 * This File is part of the Selene\Module\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Composer;

use \Selene\Module\View\RendererInterface;

/**
 * The Context object acts as a facade for the renderer object,
 * as It only allows to add context to the renderer.
 *
 * @class Context
 *
 * @package Selene\Module\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Context
{
    /**
     * view
     *
     * @var DispatcherInterface
     */
    private $view;

    /**
     * Constructor.
     *
     * @param DispatcherInterface $view
     */
    public function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * Adds a context array to the renderer context.
     *
     * @param array $context the context data
     *
     * @return Context
     */
    public function withContext(array $context)
    {
        $this->renderer->addContext($context);

        return $this;
    }

    /**
     * Adds a view instance to the renderer context.
     *
     * @param string $key the key on which the view intance is stored within
     * the context array.
     * @param mixed  $template the remplate that should ne rendered
     * @param array  $context the actual context array
     *
     * @return Context
     */
    public function nestView($key, $template, array $context)
    {
        $this->renderer->addRendererToContext($key, $template, $context);

        return $this;
    }
}
