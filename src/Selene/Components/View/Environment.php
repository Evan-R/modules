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

/**
 * @class Environment implements EnvironmentInterface
 * @see EnvironmentInterface
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Environment implements EnvironmentInterface
{
    /**
     * engineResolver
     *
     * @var EngineResolverInterface
     */
    private $engineResolver;

    /**
     * tempateResolver
     *
     * @var TemplateResolverInterface
     */
    private $tempateResolver;

    /**
     * @param EngineResolverInterface $engines
     *
     * @access public
     */
    public function __construct(
        $root = null,
        EngineResolverInterface $engines = null,
        TemplateResolverInterface $templates = null
    ) {
        $this->setTemplateResolver($root, $templates);
        $this->engineResolver = $engines ?: new EngineResolver;
    }

    /**
     * setTemplateResolver
     *
     *
     * @access protected
     * @return mixed
     */
    protected function setTemplateResolver($root = null, TemplateResolverInterface $templates = null)
    {
        if (null === $root && null === $templates) {
            throw new \InvalidArgumentException('either root or templateresolver must be given');
        } elseif (null === $root) {
            $this->templateResolver = $templates;
        } else {
            $this->templateResolver = new TemplateResolver($root);
        }
    }

    /**
     * render
     *
     * @param mixed $template
     * @param mixed $context
     *
     * @access public
     * @return string
     */
    public function render($template, $context = null)
    {
        list ($file, $engine) = $this->findEngine($template);

        return $engine->render($file, $context);
    }

    /**
     * registerEngine
     *
     * @param mixed $extension
     * @param EngineInterface $engine
     *
     * @access public
     * @return void
     */
    public function registerEngine(EngineInterface $engine)
    {
        $this->engineResolver->addEngine($engine);
    }

    /**
     * findEngine
     *
     * @param mixed $template
     *
     * @access protected
     * @return mixed
     */
    protected function findEngine($template)
    {
        foreach ($this->templateResolver->resolve($template) as $fileInfo) {
            if ($engine = $this->engineResolver->resolve($fileInfo->getExtension())) {
                return [$fileInfo->getPathInfo(), $engine];
            }
        }
    }
}
