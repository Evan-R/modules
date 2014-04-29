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

use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\DI\Loader\ConfigLoader;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Xml\Builder;
use \Selene\Components\Xml\Traits\XmlLoaderTrait;
use \Selene\Components\Confgi\Loader\AbstractXmlLoader;

/**
 * @class XmlLoader
 * @package Selene\Components\Routing\Loader
 * @version $Id$
 */
class XmlLoader extends AbstractXmlLoader
{
    protected $builder;

    /**
     * @param ContainerInterface $container
     * @param mixed $routerService
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerInterface $container, $routeBuilder = null)
    {
        $this->builder = $routeBuilder ?: new RouteBuilder;

        parent::__construct($container);
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
        $this->container->addFileResource($resource);
        $xml = $this->loadXml($resource);

        $this->parseImports($xml);
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
        foreach ($xml->xpath('//routes') as $routeNode) {

        }
    }
}
