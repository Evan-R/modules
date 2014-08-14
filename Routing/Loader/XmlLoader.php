<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Loader;

use \Selene\Module\Routing\Route;
use \Selene\Module\Routing\RouteCollectionInterface;
use \Selene\Module\Routing\Traits\RoutingLoaderTrait;
use \Selene\Module\Xml\Parser;
use \Selene\Module\Xml\Dom\DOMElement;
use \Selene\Module\Xml\Dom\DOMDocument;
use \Selene\Module\Config\Loader\XmlFileLoader;
use \Selene\Module\Config\Resource\LocatorInterface;

/**
 * @class XmlLoader extends RoutingLoader XmlLoader
 * @see RoutingLoader
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class XmlLoader extends XmlFileLoader
{
    use RoutingLoaderTrait;

    protected static $methods = ['get', 'post', 'put', 'delete'];

    /**
     * Constructor.
     *
     * @param RouteCollectionInterface $routes
     */
    public function __construct(RouteCollectionInterface $routes, LocatorInterface $locator)
    {
        $this->newBuilder($routes);
        parent::__construct($locator);
    }

    /**
     * load
     *
     * @param mixed $resource
     *
     * @return void
     */
    protected function doLoad($file)
    {
        $xml = $this->loadXml($file);

        $this->parseRoutes($xml);
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
        foreach ($xml->xpath('/routes/*') as $node) {
            $this->parseRoute($node, static::$methods);
        }
    }

    /**
     * parseRoute
     *
     * @param \DOMNode $node
     * @param array $methods
     *
     * @return void
     */
    protected function parseRoute(\DOMNode $node, array $methods = [])
    {
        if (in_array($node->nodeName, $methods)) {
            $this->parseRouteNode($node, (array)strtoupper(Parser::getPhpValue($node->nodeName)));
        } elseif ('any' === $node->nodeName) {
            $this->parseRouteNode($node, explode(',', strtoupper(join(',', $methods))));
        } elseif ('route' === $node->nodeName) {
            $this->parseRouteNode($node);
        } elseif ('routes' === $node->nodeName) {
            $this->parseGroups($node);
        } elseif ('resource' === $node->nodeName) {
            $this->parseResource($node);
        }
    }

    /**
     * parseRouteNode
     *
     * @param mixed $node
     * @param array $methods
     *
     * @return void
     */
    protected function parseRouteNode($node, array $methods = null)
    {
        $requirements = array_merge(
            $this->parseSingleRequirements($node->xpath('./@*')),
            $this->parseSingleRequirements($node->xpath('./*'))
        );

        if (null !== $methods) {
            $requirements['methods'] = $methods;
        }

        if (!isset($requirements['name'])) {
            throw new \InvalidArgumentException('Route requires a name.');
        }

        if (!isset($requirements['path'])) {
            throw new \InvalidArgumentException('Route requires a path.');
        }

        if (!isset($requirements['methods'])) {
            throw new \InvalidArgumentException('Route requires at least one method.');
        }

        if (!isset($requirements['_action'])) {
            throw new \InvalidArgumentException('Route requires an action.');
        }

        $name       = $requirements['name'];
        $path       = $requirements['path'];
        $methods    = $requirements['methods'];
        $controller = $requirements['_action'];

        foreach (['name', 'path', 'prefix', 'methods', '_action'] as $attr) {
            unset($requirements[$attr]);
        }

        //$routeClass = static::getRouteClass();
        //$route = new $routeClass($name, $path, $methods, $requirements);

        $route = $this->getRoutes()
            ->define($methods, $name, $path, $controller, $requirements);

        $this->parseDefaults($route, $node);
        $this->parseConstraints($route, $node);

        $this->getRoutes()
            ->add($route);
    }

    /**
     * parseGroups
     *
     * @param DOMElement $node
     *
     * @return void
     */
    protected function parseGroups(DOMElement $node)
    {
        $requirements = array_merge(
            $this->parseSingleRequirements($node->xpath('./@*')),
            $this->parseSingleRequirements($node->xpath('./*'))
        );

        if (!isset($requirements['prefix'])) {
            throw new \InvalidArgumentException('A routing group requires a prefix.');
        }

        $prefix = $requirements['prefix'];

        foreach (['name', 'path', 'prefix', 'methods', '_action'] as $attr) {
            unset($requirements[$attr]);
        }

        // import subroutes:
        if (0 < strlen($import = $node->getAttribute('import'))) {
            $this->getRoutes()->group($prefix, $requirements, function () use ($import) {
                $this->import($import);
            });

            return;
        }

        // parse groups:
        $this->getRoutes()->group($prefix, $requirements, function () use ($node) {
            foreach ($node->xpath('./*') as $routeNode) {
                $this->parseRoute($routeNode, static::$methods);
            }
        });
    }

    /**
     * parseResource
     *
     * @param \DOMNode $node
     *
     * @return void
     */
    protected function parseResource(\DOMNode $node)
    {
        $requirements = array_merge(
            $this->parseResourceRequirements($node->xpath('./@*')),
            $this->parseResourceRequirements($node->xpath('./*'))
        );

        if (!isset($requirements['path'])) {
            throw new \InvalidArgumentException('Resource requires a path.');
        }

        if (!isset($requirements['controller'])) {
            throw new \InvalidArgumentException('Resource requires a controller.');
        }

        $actions     = isset($requirements['actions']) ? $requirements['actions'] : null;
        $constraints = isset($requirements['constraints']) ? $requirements['constraints'] : null;

        $this->getRoutes()
            ->resource($requirements['path'], $requirements['controller'], $actions, $constraints);
    }

    /**
     * parseResourceRequirements
     *
     * @param \DOMNodeList $nodes
     *
     * @return array
     */
    protected function parseResourceRequirements(\DOMNodeList $nodes)
    {
        $requirements = [];

        foreach ($nodes as $node) {
            $name = strtolower($node->nodeName);

            switch ($name) {
                case 'path':
                case 'controller':
                case 'constraints':
                    $requirements[$name] = $this->getPhpValue($node);
                    break;
                case 'actions': $requirements[$name] = explode('|', strtolower($this->getPhpValue($node)));
                    break;
            }
        }

        return $requirements;
    }

    /**
     * parseConstraints
     *
     * @param Route $route
     * @param \DOMNode $node
     *
     * @return void
     */
    protected function parseConstraints(Route $route, \DOMNode $node)
    {
        foreach ($node->xpath('./constraints/constraint') as $constraint) {
            if (!($key = Parser::getPhpValue($constraint->getAttribute('key')))) {
                continue;
            }

            $route->setConstraint($key, $this->getPhpValue($constraint));
        }

        foreach ($node->xpath('./constraints-host/constraint|./constraintsHost/constraint') as $constraint) {
            if (!($key = Parser::getPhpValue($constraint->getAttribute('key')))) {
                continue;
            }
            $route->setHostConstraint($key, $this->getPhpValue($constraint));
        }
    }

    /**
     * parseDefaults
     *
     * @param Route $route
     * @param \DOMNode $node
     *
     * @return void
     */
    protected function parseDefaults(Route $route, \DOMNode $node)
    {
        foreach ($node->xpath('./defaults/default') as $constraint) {
            if (!($key = Parser::getPhpValue($constraint->getAttribute('key')))) {
                continue;
            }

            $route->setDefault($key, $this->getPhpValue($constraint));
        }

        foreach ($node->xpath('./defaults-host/default|./defaultsHost/default') as $constraint) {
            if (!($key = Parser::getPhpValue($constraint->getAttribute('key')))) {
                continue;
            }

            $route->setHostDefault($key, $this->getPhpValue($constraint));
        }
    }

    /**
     * parseSingleRequirements
     *
     * @param \DOMNodeList $nodeList
     *
     * @return array
     */
    protected function parseSingleRequirements(\DOMNodeList $nodeList)
    {
        $requirements = [];

        foreach ($nodeList as $node) {
            $name = strtolower(trim($node->nodeName, '_'));

            switch ($name) {
                case 'name':
                case 'path':
                case 'prefix':
                    $requirements[$name] = $this->getPhpValue($node);
                    break;
                case 'host':
                case 'action':
                case 'before':
                case 'after':
                    $requirements['_'.$name] = $this->getPhpValue($node);
                    break;
                case 'method':
                    $requirements['methods'] = explode('|', $this->getPhpValue($node));
                    break;
                case 'schemes':
                    $requirements['schemes'] = explode('|', $this->getPhpValue($node));
                    break;
            }
        }

        return $requirements;
    }

    /**
     * {@inheritdoc}
     */
    protected function notifyResource($resource)
    {
    }

    /**
     * Get the appropriate php value.
     *
     * @param \DOMNode $parameter
     * @param mixed $default
     *
     * @return mixed
     */
    private function getPhpValue(\DOMNode $parameter, $default = null)
    {
        return Parser::getPhpValue((string)$parameter->nodeValue, $default, $this->getParser());
    }
}
