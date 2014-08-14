<?php

/**
 * This File is part of the Selene\Module\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Template;

use \Selene\Module\Common\Traits\Getter;

/**
 * @class EngineResolver implements EngineResolverInterface
 * @see EngineResolverInterface
 *
 * @package Selene\Module\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class EngineResolver implements EngineResolverInterface
{
    use Getter;

    /**
     * engines
     *
     * @var array
     */
    private $engines;

    /**
     * resolvers
     *
     * @var array
     */
    private $resolvers;

    /**
     * resolvers
     *
     * @var mixed
     */
    private $aliases;

    /**
     * Constructor.
     */
    public function __construct(array $engines = [])
    {
        $this->aliases = [];
        $this->engines = [];
        $this->resolvers = [];

        $this->registerEngines($engines);
    }

    /**
     * resolver
     *
     * @param mixed $engine
     *
     * @return EngineInterface|null
     */
    public function resolve($engine)
    {
        $engine = $this->getAlias($engine);

        if (isset($this->engines[$engine])) {
            return $this->engines[$engine];
        }

        if ($this->hasResolver($engine)) {
            $this->registerEngine(call_user_func($this->resolvers[$engine]));

            return $this->resolve($engine);
        }
    }

    public function resolveByName($template)
    {
        if (null === ($extension = $this->getExtension($template))) {
            return;
        }

        return $this->resolve($extension);
    }

    /**
     * setAlias
     *
     * @param string $engine
     * @param string $alias
     *
     * @return void
     */
    public function setAlias($engine, $alias)
    {
        $this->aliases[$alias] = $engine;
    }

    /**
     * getAlias
     *
     * @param mixed $engine
     *
     * @return string
     */
    public function getAlias($engine)
    {
        return $this->getDefault($this->aliases, $engine, $engine);
    }

    /**
     * registerEngine
     *
     * @param EngineInterface $engine
     *
     * @return void
     */
    public function registerEngine(EngineInterface $engine)
    {
        $this->engines[$engine->getType()] =&$engine;
    }

    /**
     * registerEngines
     *
     * @param array $engies
     *
     * @return void
     */
    public function registerEngines(array $engines)
    {
        foreach ($engines as $engine) {
            $this->registerEngine($engine);
        }
    }

    /**
     * Register a engine resolver callback.
     *
     * The callback must return an instance of `Selene\Module\EngineInterface`
     *
     * @param string $engine
     * @param callable $resolver
     *
     * @return void
     */
    public function register($engine, callable $resolver)
    {
        $this->resolvers[(string)$engine] = &$resolver;
    }

    /**
     * Check if a engine resolver callback is set.
     *
     * @param string $engine
     *
     * @return boolean
     */
    protected function hasResolver($engine)
    {
        return isset($this->resolvers[$this->getAlias($engine)]);
    }

    /**
     * getExtension
     *
     * @param string $name
     *
     * @return string|null
     */
    protected function getExtension($name)
    {
        if (false === ($pos = strrpos($name, '.')) && 0 !== $pos) {
            return;
        }

        return substr($name, $pos + 1);
    }
}
