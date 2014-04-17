<?php

/**
 * This File is part of the Selene\Components\DI\Loaders package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loaders;

use \Selene\Components\Xml\XmlLoader as XmlFileLoader;
use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;

/**
 * @class XmlLoader
 * @package Selene\Components\DI\Loaders
 * @version $Id$
 */
class XmlLoader
{
    /**
     * loader
     *
     * @var mixed
     */
    private $loader;

    /**
     * container
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * __construct
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return mixed
     */
    public function __construct(ContainerInterface $container, XmlFileLoader $loader = null)
    {
        $this->container = $container;
        $this->loader = $loader ?: new XmlFileLoader;
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
        $xml = $this->loadXml($resource);

        $this->parseImports($xml);
        $this->parseParameters($xml);
        $this->parseServices($xml);
    }

    /**
     * parseImports
     *
     * @param DOMDocument $xml
     *
     * @access public
     * @return void
     */
    public function parseImports(DOMDocument $xml)
    {
        $imports = $xml->getElementsByTagName('imports');
        $items = $imports->item(0);

        if (!$items) {
            return;
        }

        foreach ($items->childNodes as $import) {

            if (1 !== $parameter->nodeType) {
                continue;
            }

            if (file_exists($file = $import->nodeValue)) {
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
        $params = $xml->getElementsByTagName('parameters');

        foreach ($params->item(0)->childNodes as $parameter) {

            if (1 !== $parameter->nodeType) {
                continue;
            }

            $this->container->setParameter($parameter->getAttribute('id'), $this->getPhpValue($parameter));
        }
    }

    /**
     * getParameterValue
     *
     * @param DOMElement $parameter
     *
     * @access private
     * @return void
     */
    private function getParameterValue(DOMElement $parameter)
    {
        if ($this->paramIsArray($parameter)) {
            return [];
        }

        return $this->getPhpValue($parameter);
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
     * concatParameter
     *
     * @param DOMElement $parameter
     *
     * @access private
     * @return mixed
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
     * @return mixed
     */
    private function concatParameters(DOMElement $parameter)
    {
        $parts = [];

        foreach ($parameter->xpath('items/item') as $item) {
            if ($value = $this->getAttributeValue($item, 'use', false)) {
                $parts[] = $value;
            } else {
                $parts[] = $this->getValueFromString($item->nodeValue);
            }
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
        if (0 === strlen($val)) {
            return $default;
        } elseif ($this->container->isReference($val)) {
            return new Reference(substr($val, strlen(ContainerInterface::SERVICE_REF_INDICATOR)));
        } elseif (is_numeric($val)) {
            return false !== strpos($val, '.') ? floatVal($val) : intVal($val);
        } elseif (($lval = strtolower($val)) === 'true' || $lval === 'false') {
            return $lval === 'true' ? true : false;
        }

        return $val;
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

        $this->setServiceArguments($service, $def);

        if ($alias = $this->getAttributeValue($service, 'alias')) {
            $this->container->setAlias($alias, $id);
        }

        if ($file = $this->getAttributeValue($service, 'file')) {
            $def->setFile($file);
        }

        if ($internal = $this->getAttributeValue($service, 'internal')) {
            $def->setInternal($internal);
        }

        if ($injected = $this->getAttributeValue($service, 'injected')) {
            $def->setInjected($injected);
        }

        if ($abstract = $this->getAttributeValue($service, 'abstract')) {
            $def->setAbstract($abstract);
        }

        if (null !== ($scope = $this->getAttributeValue($service, 'scope'))) {
            $def->setScope($scope);
        }

        if ($factory = $this->getAttributeValue($service, 'factory')) {
            $def->setFactory($factory);
        } elseif ($class = $this->getAttributeValue($service, 'class')) {
            $def->setClass($class);
        } else {
            throw new \RuntimeException(sprintf('either service class or factory is missing for service id %s', $id));
        }

        $this->setServiceCallers($service, $def);

        $this->container->setDefinition($id, $def);
    }

    /**
     * setServiceArguments
     *
     * @param DOMElement $service
     * @param ServiceDefinition $def
     *
     * @access private
     * @return void
     */
    private function setServiceArguments(DOMElement $service, ServiceDefinition $def)
    {
        $def->setArguments($this->getArguments($service));
    }

    /**
     * getArguments
     *
     * @param DOMElement $node
     *
     * @access private
     * @return mixed
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
     * @return mixed
     */
    private function setServiceCallers(DOMElement $service, ServiceDefinition $def)
    {
        foreach ($service->xpath('setters/setter') as $setter) {
            $def->addSetter($this->getAttributeValue($setter, 'calls'), $this->getArguments($setter));
        }
    }

    /**
     * loadXml
     *
     * @param mixed $file
     *
     * @access protected
     * @return DOMDocument
     */
    protected function loadXml($file)
    {
        $xml = $this->loader->load($file);
        return $xml;
    }

    /**
     * paramIsArray
     *
     * @param DOMElement $param
     *
     * @access private
     * @return boolean
     */
    private function paramIsArray(DOMElement $param)
    {
        return 'array' === $param->getAttribute('type');
    }

    /**
     * supports
     *
     * @param mixed $format
     *
     * @access public
     * @return mixed
     */
    public function supports($format)
    {
        return strtolower($format) === 'xml';
    }
}
