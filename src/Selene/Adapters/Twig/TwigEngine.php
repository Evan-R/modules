<?php

/**
 * This File is part of the Selene\Adapters\Twig package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Adapters\Twig;

use \Twig_Environment as TwigEnvironment;
use \Selene\Components\View\EngineInterface;

/**
 * @class TwigEngine implements EngineInterface
 * @see EngineInterface
 *
 * @package Selene\Adapters\Twig
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class TwigEngine implements EngineInterface
{
    /**
     * env
     *
     * @var Twig
     */
    private $env;

    /**
     * templateResolver
     *
     * @var mixed
     */
    private $templateResolver;

    /**
     * @param TwigEnvironment $twig
     *
     * @access public
     * @return mixed
     */
    public function __construct(TwigEnvironment $twig, ResolverInterface $templateResolver)
    {
        $this->env = $twig;
        $this->templateResolver;
    }

    /**
     * render
     *
     * @param mixed $view
     * @param mixed $context
     *
     * @access public
     * @return string
     */
    public function render($template, array $context = [])
    {
        return $this->load($template)->render($context);
    }

    /**
     * supports
     *
     * @param mixed $extension
     *
     * @access public
     * @return boolean
     */
    public function supports($extension)
    {
        return $name instanceof \Twig_Template ? true : 'twig' === $this->templateResolver->resolve($name)->getEngine();
    }

    /**
     * load
     *
     * @param mixed $template
     *
     * @access protected
     * @return mixed
     */
    protected function load($template)
    {
        if ($template instanceof \Twig_Template) {
            return $template;
        }

        return $this->env->loadTemplate((string)$template);
    }
}
