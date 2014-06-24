<?php

/**
 * This File is part of the Selene\Components\Routing\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\Routing\RouteBuilder;
use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Config\Resource\Loader;
use \Selene\Components\Config\Resource\LocatorInterface;

/**
 * @class RoutingLoader
 * @package Selene\Components\Routing\Loader
 * @version $Id$
 */
abstract class RoutingLoader extends Loader
{
    /**
     * builder
     *
     * @var mixed
     */
    protected $builder;

    /**
     * container
     *
     * @var mixed
     */
    protected $container;

    /**
     * routes
     *
     * @var mixed
     */
    protected $routes;

    /**
     * @var string
     */
    protected $routesId;

    /**
     * @param BuilderInterface $builder
     * @param LocaltorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator, $routesId = 'routes')
    {
        $this->routesId = $routesId;

        $this->container = $builder->getContainer();
        $this->builder = $builder;
        $this->routes = new RouteBuilder;

        parent::__construct($locator);
    }

    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    public function load($resource, $any = false)
    {
        foreach ($this->locator->locate($resource, true) as $file) {
            $this->doLoad($file);
            $this->builder->addFileResource($file);
        }

        $this->prepareContainer();
    }

    /**
     * prepareContainer
     *
     *
     * @access protected
     * @return void
     */
    protected function prepareContainer()
    {
        if (!$this->container->hasDefinition($this->routesId)) {
            $this->container->define($this->routesId, $this->getRouteCollectionClass());
        }

        $routes = $this->container->get($this->routesId);

        $this->container->get($this->routesId)->merge($this->routes->getRoutes());
    }

    /**
     * getRouteCollectionClass
     *
     * @access protected
     * @return string
     */
    protected function getRouteCollectionClass()
    {
        return '\Selene\Components\Routing\RouteCollection';
    }
}
