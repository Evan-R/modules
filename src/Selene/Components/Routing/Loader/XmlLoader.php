<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\Config\Traits\XmlLoaderHelperTrait;

/**
 * @class XmlLoader extends RoutingLoader XmlLoader
 * @see RoutingLoader
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XmlLoader extends RoutingLoader
{
    use XmlLoaderHelperTrait;

    /**
     * load
     *
     * @param mixed $resource
     *
     * @access public
     * @return mixed
     */
    protected function doLoad($file)
    {
        $xml = $this->loadXml($file);
        $this->parseRoutes($xml);
    }

    protected function setRequirement($route, array $data, $attribute)
    {
        if (!isset($data[$attribute])) {
            return;
        }

        $route->setRequirement('_'.$attribute, $data[$attribute]);
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

        $this->setRequirement($route, $values, 'host');

        $route->setAction($values['action']);

        $filterNodes = $node->getElementsByTagName('filters');

        if (isset($values['filters'])) {
            $this->setRouteFilters($route, $values['filters']);
        }

    }

    /**
     * setRouteFilters
     *
     * @param mixed $route
     * @param mixed $filters
     *
     * @access protected
     * @return void
     */
    protected function setRouteFilters($route, $filters)
    {
        foreach ($filters as $key => $filter) {
            if ('before' === $key) {
                $route->setBeforeFilters($filter);
            } elseif ('after' === $key) {
                $route->setAfterFilters($filter);
            }
        }
    }

    protected function parseGroups(DOMElement $routes)
    {
        //var_dump($routes);
    }

    protected function parseResources(DOMElement $routes)
    {
        return null;
    }
}
