<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Loader;

use \Selene\Module\Xml\Parser;
use \Selene\Module\Xml\Dom\DOMElement;
use \Selene\Module\Xml\Dom\DOMDocument;
use \Selene\Module\DI\Reference;
use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Definition\ParentDefinition;
use \Selene\Module\DI\Definition\ServiceDefinition;
use \Selene\Module\DI\Definition\DefinitionInterface;
use \Selene\Module\Config\Resource\LocatorInterface;
use \Selene\Module\Config\Loader\XmlFileLoader;
use \Selene\Module\Config\Traits\ContainerBuilderAwareLoaderTrait;
use \Selene\Module\Common\Helper\ListHelper;

/**
 * @class XmlLoader XmlLoader
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XmlLoader extends XmlFileLoader
{
    use ContainerBuilderAwareLoaderTrait;

    /**
     * Constructor.
     *
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @access public
     */
    public function __construct(BuilderInterface $builder, LocatorInterface $locator)
    {
        parent::__construct($locator);

        $this->container = $builder->getContainer();
        $this->setBuilder($builder);
    }

    protected function doLoad($file)
    {
        $xml = $this->loadXml($file);
        // parse the imported xml files
        $this->parseImports($xml, $file);
        // parse the container parareters
        $this->parseParameters($xml);
        // parse the container services
        $this->parseServices($xml);

        $this->prepareParsePackageConfig($xml);
    }

    /**
     * prepareParsePackageConfig
     *
     * @param DOMDocument $xml
     *
     * @return void
     */
    protected function prepareParsePackageConfig(DOMDocument $xml)
    {
        $key = $this->getParser()->getIndexKey();

        $this->getParser()->setIndexKey('item');
        $this->parsePackageConfig($xml);
        $this->getParser()->setIndexKey($key);
    }

    /**
     * parsePackageConfig
     *
     * @param mixed $xml
     *
     * @access protected
     * @return void
     */
    protected function parsePackageConfig($xml)
    {
        $key = $this->getPackageAttributeKey();

        foreach ($xml->xpath('../*[@'.$key.']|*[@'.$key.']') as $package) {

            if (!$alias = $this->getPackageNameAlias($package, $key)) {
                continue;
            }

            $values = [];

            foreach ($package->xpath("*[local-name() != 'import']") as $item) {
                $key = Parser::fixNodeName($item->nodeName);
                $this->builder->addPackageConfig(
                    $alias,
                    [$key => $this->getParser()->parseDomElement($item)]
                );
                $values[] = [$key => $this->getParser()->parseDomElement($item)];
            }
        }
    }

    /**
     * getPackageNameAlias
     *
     * @param DOMElement $package
     * @param string $key
     *
     * @return string|null
     */
    protected function getPackageNameAlias(DOMElement $package, $key)
    {
        return $this->getAttributeValue($package, $key, false);
    }

    /**
     * getPackageAttributeKey
     *
     * @return string
     */
    protected function getPackageAttributeKey()
    {
        return 'package';
    }

    /**
     * Parse imported resources.
     *
     * @param DOMDocument $xml the resource Document
     *
     * @access protected
     * @return void
     */
    protected function parseImports(DOMDocument $xml, $file)
    {
        //$this->setResourcePath(dirname($file));

        foreach ($xml->xpath('//imports/import') as $import) {
            $path = Parser::getElementText($import);
            $this->import($path);
        }
    }

    /**
     * checkImportPathIntegrity
     *
     * @param mixed $path
     *
     * @access protected
     * @return mixed
     */
    //protected function checkPathIntegrity($path)
    //{
    //    return stream_is_local($path) && is_file($path);
    //}

    /**
     * Parse parameter nodes of the resource file.
     *
     * @param DOMDocument $xml the resource Document.
     *
     * @access private
     * @return void
     */
    protected function parseParameters(DOMDocument $xml)
    {
        foreach ($xml->xpath('/container/parameters/parameter') as $parameter) {
            $this->container->setParameter($parameter->getAttribute('id'), $this->getPhpValue($parameter));
        }
    }

    /**
     * Parse service nodes of the resource file.
     *
     * @access private
     * @return void
     */
    private function parseServices($xml)
    {
        $services = $xml->xpath('/container/services/service');

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

            if (0 < $item->xpath('items')->length) {
                $value = $this->getParameterArray($item);
            } elseif (!($value = $this->getAttributeValue($item, 'use', false))) {
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

    private function parseMetaData(DOMElement $service, DefinitionInterface $definition)
    {
        foreach ($service->xpath('meta') as $tagNode) {

            if (!($name = $this->getAttributeValue($tagNode, 'name'))) {
                continue;
            }

            $attrs = [];

            $dataAttrs = [];

            foreach ($tagNode->xpath("./@*[name() != 'name' and name() != 'data']") as $attribute) {
                $attrs[] = [Parser::fixNodeName($attrName = $attribute->nodeName) => $this->getAttributeValue($tagNode, $attrName)];
            }

            foreach ($tagNode->xpath("data/@*[name() != 'name']") as $attribute) {
                $dataAttrs[] = [
                    Parser::fixNodeName($attrName = $attribute->nodeName) => $this->getAttributeValue($attribute->parentNode, $attrName)
                ];
            }

            $definition->setMetaData($name, array_merge($attrs, $dataAttrs));
        }
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
     * @throws \InvalidArgumentException if class attribute is missing.
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
                call_user_func([$def, $method], $value);
            }
        }

        if ($def->hasParent()) {
            $def = new ParentDefinition($def->getParent());
        }

        $this->parseMetaData($service, $def);

        if ($class = $this->getAttributeValue($service, 'class', false)) {
            $def->setClass($class);
        }

        if ($factory = $this->getAttributeValue($service, 'factory', false)) {
            $method = $this->getAttributeValue($service, 'factory-method', 'make');
            $def->setFactory($factory, $method);
        }

        if (!$class && !$def instanceof ParentDefinition) {
            throw new \InvalidArgumentException(
                sprintf('Definition "%s" must define its class unless it has a parent definition', $id)
            );
        }

        $this->setDefinitionArguments($service, $def);

        $this->setServiceCallers($service, $def);

        $this->container->setDefinition($id, $def);
    }

    private function setDefinitionArguments(DOMElement $service, DefinitionInterface $definition)
    {
        if (!$definition instanceof ParentDefinition) {
            $definition->setArguments($this->getArguments($service));
        }

        foreach ($this->getReplacementArguments($service) as $index => $argument) {
            $definition->replaceArgument($argument, $index);
        }
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

        foreach ($node->xpath('arguments/argument[not(index)]') as $argument) {

            $arguments[] = $val = $this->getPhpValue($argument);
        }

        return $arguments;
    }

    /**
     * Get all arguments contained on a <arguments> node.
     *
     * @param DOMElement $node the <arguments> node.
     *
     * @access private
     * @return array
     */
    private function getReplacementArguments(DOMElement $node)
    {
        $arguments = [];

        foreach ($node->xpath('arguments/argument[@index]') as $argument) {
            $index = $this->getAttributeValue($argument, 'index');
            $arguments[$index] = $this->getPhpValue($argument);
        }

        return $arguments;
    }

    /**
     * Set method callers for a service definition based on the setters nodes
     * of the service node.
     *
     * @param DOMElement $service the service node.
     * @param \Selene\Module\DI\Definition\ServiceDefinition $def the
     * service definition.
     *
     * @access private
     * @return void
     */
    private function setServiceCallers(DOMElement $service, DefinitionInterface $def)
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
            //return new Reference(substr($val, strlen(ContainerInterface::SERVICE_REF_INDICATOR)));
        }

        return Parser::getPhpValue($val, $default, $this->getParser());
    }
}
