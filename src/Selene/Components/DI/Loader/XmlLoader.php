<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Xml\Builder;
use \Selene\Components\Xml\Traits\XmlLoaderTrait;
use \Selene\Components\DI\Definition\ServiceDefinition;

/**
 * @class XmlLoader XmlLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XmlLoader extends ConfigLoader
{
    use XmlLoaderTrait {
        XmlLoaderTrait::create as private createNew;
        XmlLoaderTrait::getErrors as private getXmlErrors;
        XmlLoaderTrait::load as private loadXml;
        XmlLoaderTrait::getOption as private getXmlOption;
        XmlLoaderTrait::setOption as private setXmlOption;
        XmlLoaderTrait::handleXmlErrors as private handleXmlRuntimeErrors;
    }

    /**
     * loader
     *
     * @var mixed
     */
    private $loader;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->setXmlOption('simplexml', false);
        $this->setXmlOption('from_string', false);
    }

    /**
     * load
     *
     * @TODO add DTD schema check.
     * @param mixed $resource
     *
     * @access public
     * @return void
     */
    public function load($resource)
    {
        $this->container->addFileResource($resource);

        $xml = $this->loadXml($resource);

        $this->parseImports($xml, $resource);
        $this->parseParameters($xml);
        $this->parseServices($xml);
    }

    /**
     * supports
     *
     * @param mixed $format
     *
     * @access public
     * @return boolean
     */
    public function supports($format)
    {
        return 'xml' === strtolower($format);
    }

    /**
     * parseImports
     *
     * @param DOMDocument $xml
     *
     * @access private
     * @return void
     */
    private function parseImports(DOMDocument $xml, $file)
    {
        foreach ($xml->xpath('//imports/import') as $import) {

            if (file_exists($file = dirname($file).DIRECTORY_SEPARATOR.$import->nodeValue)) {
                $loader = new static($this->container, $this->loader);
                $loader->load($file);
            }
        }
    }

    /**
     * parseParameters
     *
     * @param DOMDocument $xml
     *
     * @access private
     * @return void
     */
    private function parseParameters(DOMDocument $xml)
    {
        foreach ($xml->xpath('//parameters/parameter') as $parameter) {
            $this->container->setParameter($parameter->getAttribute('id'), $this->getPhpValue($parameter));
        }
    }

    /**
     * parseServices
     *
     * @access private
     * @return void
     */
    private function parseServices($xml)
    {
        $services = $xml->xpath('//services/service');

        foreach ($services as $service) {
            $this->setServiceDefinition($service);
        }
    }

    /**
     * concatParameter
     *
     * @param DOMElement $parameter
     *
     * @access private
     * @return array
     */
    private function getParameterArray(DOMElement $parameter)
    {
        $array = [];

        foreach ($parameter->xpath('items/item') as $item) {

            if (!($value = $this->getAttributeValue($item, 'use', false))) {
                $value = $this->getValueFromString($item->nodeValue);
            }

            if ($key = $this->getAttributeValue($item, 'key', false)) {
                $array[$key] = $value;
            } else {
                $array[] = $value;
            }
        }
        return $array;
    }

    /**
     * concatParameter
     *
     * @param DOMElement $parameter
     *
     * @access private
     * @return string
     */
    private function concatParameters(DOMElement $parameter)
    {
        $parts = [];

        foreach ($parameter->xpath('items/item') as $item) {

            if (!($value = $this->getAttributeValue($item, 'use', false))) {
                $value = $this->getValueFromString($item->nodeValue);
            }

            $parts[] = $value;
        }
        return implode('', $parts);
    }

    /**
     * getAttributeValue
     *
     * @param mixed $node
     * @param mixed $attr
     * @param mixed $default
     *
     * @access private
     * @return mixed
     */
    private function getAttributeValue(DOMElement $node, $attr, $default = null)
    {
        return $this->getValueFromString($node->getAttribute($attr), $default);
    }

    /**
     * setServiceDefinition
     *
     * @param mixed $service
     *
     * @access private
     * @return void
     */
    private function setServiceDefinition($service)
    {
        $def = new ServiceDefinition;
        $id  = $this->getAttributeValue($service, 'id');


        if ($alias = $this->getAttributeValue($service, 'alias')) {
            $this->container->setAlias($alias, $id);
        }

        foreach (['file', 'parent', 'injected', 'internal', 'abstract', 'scope'] as $attribute) {

            if ($value = $this->getAttributeValue($service, $attribute)) {

                $method = 'set'.ucfirst($attribute);
                call_user_func($dev, $method, $value);
            }

        }

        if ($class = $this->getAttributeValue($service, 'class', false)) {
            $def->setClass($class);
        } elseif ($factory = $this->getAttributeValue($service, 'factory', false)) {
            $def->setFactory($factory);
        } else {
            throw new \RuntimeException(
                sprintf('either service class or factory is missing for service id %s', $id)
            );
        }

        $def->setArguments($this->getArguments($service));

        $this->setServiceCallers($service, $def);

        $this->container->setDefinition($id, $def);
    }

    /**
     * getArguments
     *
     * @param DOMElement $node
     *
     * @access private
     * @return array
     */
    private function getArguments(DOMElement $node)
    {
        $arguments = [];

        foreach ($node->xpath('arguments/argument') as $argument) {

            $arguments[] = $this->getPhpValue($argument);
        }

        return $arguments;
    }

    /**
     * setServiceCallers
     *
     * @param DOMElement $service
     * @param ServiceDefinition $def
     *
     * @access private
     * @return void
     */
    private function setServiceCallers(DOMElement $service, ServiceDefinition $def)
    {
        foreach ($service->xpath('setters/setter') as $setter) {

            $def->addSetter($this->getAttributeValue($setter, 'calls'), $this->getArguments($setter));
        }
    }

    /**
     * getPhpValue
     *
     * @param mixed $parameter
     *
     * @access private
     * @return mixed
     */
    private function getPhpValue($parameter)
    {
        $type = $parameter->getAttribute('type');

        if ('array' === $type) {
            return $this->getParameterArray($parameter);
        } elseif ('string' === $type) {
            return (string)$parameter->nodeValue;
        } elseif ('constant' === $type && defined($const = (string)$parameter->nodeValue)) {
            return constant($const);
        } elseif ('concat' === $type) {
            return $this->concatParameters($parameter);
        }
        return $this->getValueFromString((string)$parameter->nodeValue);
    }

    /**
     * getValueFromString
     *
     * @param mixed $val
     * @param mixed $default
     *
     * @access private
     * @return mixed
     */
    private function getValueFromString($val, $default = null)
    {
        if ($this->container->isReference($val)) {
            return new Reference(substr($val, strlen(ContainerInterface::SERVICE_REF_INDICATOR)));
        }

        return Builder::getPhpValue($val, $default);
    }
}
