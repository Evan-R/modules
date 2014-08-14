<?php

/**
 * This File is part of the Selene\Module\View\Composer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Composer;

use \Selene\Module\View\RendererInterface;

/**
 * @class Context
 * @package Selene\Module\View\Composer
 * @version $Id$
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
     * addContext
     *
     * @param array $context
     *
     * @return Context
     */
    public function withContext(array $context)
    {
        $this->renderer->addContext($context);

        return $this;
    }

    /**
     * nestView
     *
     * @param mixed $key
     * @param mixed $template
     * @param array $context
     *
     * @return Context
     */
    public function nestView($key, $template, array $context)
    {
        $this->renderer->addRendererToContext($key, $template, $context);

        return $this;
    }
}
