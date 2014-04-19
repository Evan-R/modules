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
use \Selene\Components\Config\Loader\AbstractXmlLoader;
use \Selene\Components\DI\Definition\ServiceDefinition;

/**
 * @class XmlLoader XmlLoader
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XmlLoader extends AbstractXmlLoader
{
    /**
     * Loads the resource file into the container.
     *
     * @TODO add DTD schema check.
     * @param string $resource the xml file resource.
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
     * {@inheritdoc}
     * @param string $format
     */
    public function supports($format)
    {
        return 'xml' === strtolower($format);
    }

    /**
     * Parse imported resources.
     *
     * @param DOMDocument $xml the resource Document
     *
     * @access private
     * @return void
     */
    private function parseImports(DOMDocument $xml, $file)
    {
        foreach ($xml->xpath('//imports/import') as $import) {

            if (file_exists($file = dirname($file).DIRECTORY_SEPARATOR.$import->nodeValue)) {
                $loader = new static($this->container);
                $loader->load($file);
            }
        }
    }

    /**
     * Parse parameter nodes of the resource file.
     *
     * @param DOMDocument $xml the reource Document.
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
     * paser service nodes of the resource file.
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
     * Parse a parameter with type array.
     *
     * @param DOMElement $parameter the parameter node.
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
     * Parse a parameter node marked as concat.
     *
     * @param DOMElement $parameter the parameter node.
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
     * Gets a value from a no attribute,
     *
     * @param DOMElement $node the node the contains the attribute.
     * @param string     $attr the name of the attribute.
     * @param mixed      $default the default value to return if no attribute was
     * found.
     *
     * @access private
     * @return mixed will return a corresponding php value.
     */
    private function getAttributeValue(DOMElement $node, $attr, $default = null)
    {
        return $this->getValueFromString($node->getAttribute($attr), $default);
    }

    /**
     * Set service definitions on the container based on the xml service nodes.
     *
     * @param DOMElement $service the service node.
     *
     * @access private
     * @return void
     */
    private function setServiceDefinition(DOMElement $service)
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
     * Get all arguments contained on a <arguments> node.
     *
     * @param DOMElement $node the <arguments> node.
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
     * Set method callers for a service definition based on the setters nodes
     * of the service node.
     *
     * @param DOMElement $service the service node.
     * @param \Selene\Components\DI\Definition\ServiceDefinition $def the
     * service definition.
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
     * Get a corresponding php value from a parameter node.
     *
     * @param DOMElement $parameter the parameter node.
     *
     * @access private
     * @return mixed
     */
    private function getPhpValue(DOMElement $parameter)
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
     * Get corresponding php value from a string derived from an xml node.
     *
     * @param string $val the input value
     * @param mixed  $default the value to return if conversion fails.
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
