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
use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\DI\Loader\ConfigLoader;
use \Selene\Components\Config\Traits\XmlLoaderHelperTrait;
use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Xml\Builder;
use \Selene\Components\Xml\Traits\XmlLoaderTrait;
use \Selene\Components\Config\Resource\Loader;
use \Selene\Components\Config\Resource\Locator;

/**
 * @class XmlLoader
 * @package Selene\Components\Routing\Loader
 * @version $Id$
 */
class XmlLoader extends Loader
{
    use XmlLoaderHelperTrait;

    protected $builder;

    /**
     * container
     *
     * @var mixed
     */
    protected $container;
    protected $locator;

    /**
     * @param ContainerInterface $container
     * @param mixed $routerService
     *
     * @access public
     * @return mixed
     */
    public function __construct(BuilderInterface $builder, Locator $locator)
    {
        $this->builder = $builder;
        $this->container = $builder->getContainer();
        $this->routes = new RouteBuilder;

        //if ($this->container->hasDefinition('routes')) {
            //$this->container->get('routes')->merge($this->;
        //}

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
    public function load($resource)
    {
        if (!($file = $this->locator->locate($resource))) {
            return;
        }

        $xml = $this->loadXml($file);

        $this->builder->addFileResource($file);

        $this->parseRoutes($xml);

        if (!$this->container->hasDefinition('routes')) {
            $this->container->define('routes', '\Selene\Components\Routing\RouteCollection');
        }

        $routes = $this->container->get('routes');

        $this->container->get('routes')->merge($this->routes->getRoutes());
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

    /**
     * parseRoutes
     *
     * @param DOMDocument $xml
     *
     * @access protected
     * @return void
     */
    protected function parseRoutes(DOMDocument $xml)
    {
        foreach ($xml->xpath('/router/routes/*') as $routeNode) {
            if ('route' === $routeNode->nodeName) {
                $this->parseRoute($routeNode);
            } elseif ('resource' === $routeNode->nodeName) {
                $this->parseResource($routeNode);
            } elseif ('routes' === $routeNode->nodeName) {
                $this->parseGroups($routeNode);
            }
        }
    }

    protected function parseRoute(DOMElement $node)
    {
        $values = $this->getParser()->parseDomElement($node);
        $route = $this->routes->define($values['method'], $values['name'], $values['pattern']);

        $route->setAction($values['action']);

    }

    protected function parseGroups(DOMElement $routes)
    {
        return null;
    }

    protected function parseResources(DOMElement $routes)
    {
        return null;
    }
}
