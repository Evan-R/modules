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
 * @class EngineResolver implements EngineResolverInterface
 * @see EngineResolverInterface
 *
 * @package Selene\Components\View
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class EngineResolver implements EngineResolverInterface
{
    /**
     * engines
     *
     * @var array
     */
    private $engines;

    /**
     * templates
     *
     * @var TemplateResolverInterface
     */
    private $templates;

    /**
     * @param TemplateResolverInterface $templates
     * @param array $engines
     *
     * @access public
     */
    public function __construct(array $engines = [])
    {
        $this->engines = $engines;
    }

    /**
     * resolve
     *
     * @param string $template
     *
     * @access public
     * @return mixed
     */
    public function resolve($extension)
    {
        foreach ($this->engines as $engine) {
            if ($engine->supports($extension)) {
                return $engine;
            }
        }

        throw new \InvalidArgumentException(sprintf('No suitable engine found for extension %s', $extension));
    }

    /**
     * addEngine
     *
     * @param mixed $extension
     * @param EngineInterface $engine
     *
     * @access public
     * @return void
     */
    public function addEngine(EngineInterface $engine)
    {
        $this->engines[] = $engine;
    }

    /**
     * getTemplates
     *
     * @access protected
     * @return TemplateResolverInterface
     */
    protected function getTemplates()
    {
        return $this->templates;
    }

    /**
     * getEngines
     *
     *
     * @access protected
     * @return array
     */
    protected function getEngines()
    {
        return $this->engines;
    }

    /**
     * getExctension
     *
     * @param mixed $template
     *
     * @access protected
     * @return mixed
     */
    protected function getExtension($template)
    {
        return $template;
    }
}
