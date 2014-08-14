<?php

/**
 * This File is part of the Selene\Module\Routing\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Loader;

use \Selene\Module\Routing\RouteBuilder;
use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Config\Resource\Loader;
use \Selene\Module\Config\Resource\LocatorInterface;
use \Selene\Module\Routing\RouteCollectionInterface;

/**
 * @class RoutingLoader
 * @package Selene\Module\Routing\Loader
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

    protected $loaded;

    /**
     * @param BuilderInterface $builder
     * @param LocaltorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator, RouteCollectionInterface $routes)
    {
        $this->container = $builder->getContainer();
        $this->builder = $builder;
        $this->routes = new RouteBuilder($routes);

        $this->loaded = [];

        parent::__construct($locator);
    }

    /**
     * getRouteBuilder
     *
     * @return RouteBuilder
     */
    public function getRouteBuilder()
    {
        return $this->routes;
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

            if (in_array($rpath = realpath($file), $this->loaded)) {
                continue;
            }

            $this->doLoad($file);
            $this->builder->addFileResource($file);
            $this->loaded[] = $rpath;
        }
    }
}
