<?php

/**
 * This File is part of the Selene\Module\View package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Template;

/**
 * @class PhpEngine implements EngineInterface
 * @see EngineInterface
 *
 * @package Selene\Module\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class PhpEngine implements EngineInterface
{
    /**
     * loader
     *
     * @var mixed
     */
    private $loader;

    /**
     * resolver
     *
     * @var mixed
     */
    private $resolver;

    /**
     * templates
     *
     * @var array
     */
    private $templates;

    /**
     * @param ResolverInterface $templateResolver
     * @param LoaderInterface $templateLoader
     *
     * @access public
     */
    public function __construct(ResolverInterface $templateResolver, LoaderInterface $templateLoader)
    {
        $this->loader = $templateLoader;
        $this->resolver = $templateResolver;
        $this->templates = [];
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
        return null;
    }

    /**
     * supports
     *
     * @param mixed $name
     *
     * @access public
     * @return boolean
     */
    public function supports($name)
    {

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
        // do stuff
        // cache template
    }
}
