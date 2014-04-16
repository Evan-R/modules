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
     * extensions
     *
     * @var array
     */
    private $extensions;

    /**
     * @param TwigEnvironment $twig
     *
     * @access public
     * @return mixed
     */
    public function __construct(TwigEnvironment $twig)
    {
        $this->env = $twig;
        $this->extensions = ['twig'];
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
    public function render($file, $context = null)
    {
        $this->env->loadTemplate($file);
        return $this->env->render($context);
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
        return in_array($extension, $this->extensions);
    }
}
