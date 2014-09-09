<?php

/*
 * This File is part of the Selene\Module\Routing\Dumper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Dumper;

use \DOMElement;
use \DOMDocument;
use \Selene\Module\Routing\Route;
use \Selene\Module\Routing\RouteCollectionInterface;

/**
 * Dumps a route collection into an xml string.
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class XmlDumper implements DumperInterface
{
    const OUTPUT_STRING = 230;

    const OUTPUT_DOM = 231;

    /**
     * Constructor.
     *
     * @param mixed $option
     *
     * @return void
     */
    public function __construct($option = null)
    {
        $this->outputOption = $option ?: self::OUTPUT_STRING;
    }

    /**
     * dump
     *
     * @return void
     */
    public function dump(RouteCollectionInterface $routes)
    {
        $dom = new DOMDocument;
        $dom->formatOutput = true;
        $dom->appendChild($child = new DOMElement('routes'));

        $this->createRouteNodes($routes, $child);

        return 0 === ($this->outputOption & ~self::OUTPUT_STRING) ? $dom->saveXML() : $dom;
    }

    /**
     * dumpRoutes
     *
     * @param RouteCollectionInterface $routes
     *
     * @return string
     */
    public static function dumpRoutes(RouteCollectionInterface $routes)
    {
        return (new static)->dump($routes);
    }

    /**
     * createRouteNodes
     *
     * @param DOMElement $routes
     *
     * @return void
     */
    private function createRouteNodes(RouteCollectionInterface $routes, DOMElement $routeNode)
    {
        foreach ($routes as $name => $route) {
            $this->createRouteNode($name, $route, $routeNode);
        }
    }

    /**
     * createRouteNode
     *
     * @param mixed $name
     * @param Route $route
     * @param DOMElement $parent
     *
     * @return void
     */
    private function createRouteNode($name, Route $route, DOMElement $parent)
    {
        $parent->appendChild($node = new DOMElement('route'));

        $node->setAttribute('name', $name);
        $node->setAttribute('path', $route->getPattern());
        $node->setAttribute('method', implode('|', $route->getMethods()));
        $node->setAttribute('action', $route->getAction());

        if ($host = $route->getHost()) {
            $node->setAttribute('host', $host);
        }
        $this->appendRouteRequirements($route, $node);

        $this->setDefaults($route, $node);

        //$parent->appendChild($node);
    }

    /**
     * setDefaults
     *
     * @param Route $route
     * @param DOMElement $node
     *
     * @return void
     */
    private function setDefaults(Route $route, DOMElement $node)
    {
        $defaults = $route->getDefaults();
        $hostDefaults = $route->getHostDefaults();

        if (!empty($defaults)) {
            $node->appendChild($child = new DOMelement('defaults'));
            $this->addValues($child, $defaults, 'default');
        }

        if (!empty($hostDefaults)) {
            $node->appendChild($child = new DOMelement('defaultsHost'));
            $this->addValues($child, $hostDefaults, 'default');
        }
    }

    /**
     * appendRouteRequirements
     *
     * @param Route $route
     * @param DOMElement $parent
     *
     * @return void
     */
    private function appendRouteRequirements(Route $route, DOMElement $parent)
    {
        foreach ($route->getRequirements() as $requirement => $value) {

            if (in_array($requirement, ['_host', '_action', '_methods'])) {
                continue;
            }

            if ('_schemes' === $requirement) {
                if (!(1 === count($value) && 'http' === $value[0])) {
                    $parent->appendChild($node = new DOMElement(trim($requirement, '_'), implode('|', $value)));
                }
                continue;
            }

            if ('_constraints' === $requirement) {

                if (isset($value['route'])) {
                    $parent->appendChild($node = new DOMElement('constraints'));
                    $this->addValues($node, $value['route'], 'constraint');
                }

                if (isset($value['host'])) {
                    $parent->appendChild($node = new DOMElement('constraintsHost'));
                    $this->addValues($node, $value['host'], 'constraint');
                }

                continue;
            }

            // sets the remaining requirements;
            if (!empty($value)) {
                $parent->appendChild(
                    $node = new DOMElement(trim($requirement, '_'), is_array($value) ? implode('|', $value) : $value)
                );
            }
        }
    }

    /**
     * addValues
     *
     * @param mixed $node
     * @param array $values
     * @param string $name
     *
     * @return void
     */
    private function addValues($node, array $values, $name)
    {
        foreach ($values as $key => $value) {
            $node->appendChild($child = new DOMElement($name, $value));
            $child->setAttribute('key', $key);
        }
    }
}
