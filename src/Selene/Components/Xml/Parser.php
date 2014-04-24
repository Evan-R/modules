<?php

/**
 * This File is part of the Selene\Components\Xml package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Xml;

use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;

/**
 * @class Parser
 * @package Selene\Components\Xml
 * @version $Id$
 */
class Parser
{
    /**
     * pluralizer
     *
     * @var mixed
     */
    private static $pluralizer;

    /**
     * setPluralizer
     *
     * @param callable $pluralizer
     *
     * @access public
     * @return void
     */
    public static function setPluralizer(callable $pluralizer = null)
    {
        static::$pluralizer = $pluralizer;
    }

    /**
     * parse
     *
     * @param DOMDocument $xml
     *
     * @access public
     * @return array
     */
    public static function parse(\DOMDocument $xml, $mergeAttributes = true, array $arrayKeys = [])
    {
        if (!$xml instanceof DOMDocument) {
            $xml = static::convertDocument($xml);
        }

        $root = $xml->documentElement;

        $children = static::parseDomElement($root, $mergeAttributes);

        $results = [$xml->documentElement->nodeName => $children];

        return $results;
    }

    /**
     * parseDomElement
     *
     * @param DOMelement $xml
     *
     * @access public
     * @return null|array
     */
    public static function parseDomElement(DOMelement $xml, $mergeAttributes = true)
    {
        $attributes = static::parseElementAttributes($xml);

        $hasAttributes = (bool)$attributes;

        $text = static::prepareTextValue($xml, current($attributes));

        $result = static::parseElementNodes($xml->xpath('./child::*'), $xml->nodeName, $mergeAttributes);

        if ($hasAttributes) {

            if (null !== $text) {
                $result['value'] = $text;
            }

            if ($mergeAttributes) {
                $attributes = $attributes['@attributes'];
            }

            $result = array_merge($attributes, $result);
            return $result;
        }

        if (null !== $text) {
            if (!empty($result)) {
                $result['value'] = $text;
            } else {
                $result = $text;
            }
            return $result;
        }

        return (!(bool)$result && null === $text) ? null : $result;
    }

    /**
     * parseElementAttributes
     *
     * @param DOMelement $xml
     * @param string $attrKey
     *
     * @access private
     * @return mixed
     */
    private static function parseElementAttributes(DOMelement $xml, $attrKey = '@attributes')
    {
        $elementAttrs = $xml->xpath('./@*');

        if (0 === $elementAttrs->length) {
            return [];
        }

        $attrs = [];

        foreach ($elementAttrs as $key => $attribute) {

            $namespace = $attribute->namespaceURI;

            $value = static::getPhpValue($attribute->nodeValue);

            if ($prefix = $attribute->prefix) {
                $attName = static::prefixKey($attribute->nodeName, $prefix);
            } else {
                $attName  = $attribute->nodeName;
            }

            $attrs[$attName] = $value;
        }

        return [$attrKey => $attrs];
    }

    /**
     * getPhpValue
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public static function getPhpValue($val, $default = null, $mergeAttributes = true)
    {
        if ($val instanceof \DOMElement) {
            return static::parseDomElement($val, $mergeAttributes);
        }

        if (0 === strlen($val)) {
            return $default;
        }

        if (is_numeric($val)) {
            return ctype_digit($val) ? intval($val) : floatval($val);
        } elseif (($lval = strtolower($val)) === 'true' || $lval === 'false') {
            return $lval === 'true' ? true : false;
        }

        return $val;
    }

    /**
     * getElementText
     *
     * @param DOMElement $element
     * @param mixed $concat
     *
     * @access private
     * @return mixed
     */
    public static function getElementText(DOMElement $element, $concat = true)
    {
        $textNodes = [];

        foreach ($element->xpath('./text()') as $text) {
            if ($value = clearValue($text->nodeValue)) {
                $textNodes[] = $value;
            }
        }
        return $concat ? implode($textNodes) : $textNodes;
    }

    /**
     * convert boolish and numeric values
     *
     * @param mixed $text
     * @param array $attributes
     */
    private static function prepareTextValue(DOMElement $xml, $attributes = null)
    {
        $text = static::getElementText($xml, true);
        return (isset($attributes['type']) && 'text' === $attributes['type']) ?
            clearValue($text) :
            static::getPhpValue($text);
    }

    /**
     * parseElementNodes
     *
     * @param DOMElement $child
     *
     * @access private
     * @return array
     */
    private static function parseElementNodes($children, $parentName = null, $mergeAttributes = true)
    {
        $result = [];

        foreach ($children as $child) {
            $prefix = $child->prefix ?: null;
            $nsURL = $child->namespaceURI ?: null;

            $oname = $child->nodeName;
            $name = null === $prefix ? $oname : static::prefixKey($oname, $prefix);

            if ($children->length < 2) {
                $result[$name] = static::getPhpValue($child, null, $mergeAttributes);
                break;
            }

            if (isset($result[$name])) {
                if (is_array($result[$name]) && arrayNumeric($result[$name])) {
                    $value = static::getPhpValue($child, null, $mergeAttributes);
                    //$value = static::parseDomElement($child, $mergeAttributes);
                    if (is_array($value) && arrayNumeric($value)) {
                        $result[$name] = array_merge($result[$name], $value);
                    } else {
                        $result[$name][] = $value;
                    }
                } else {
                    continue;
                }
            } else {

                $equals = static::getEqualNodes($child, $prefix);
                $value = static::getPhpValue($child, null, $mergeAttributes);

                if (1 < $equals->length) {
                    if (static::isEqualOrPluralOf($parentName, $oname)) {
                        $result[] = static::getPhpValue($child, null, $mergeAttributes);
                    } else {
                        $plural = static::pluralize($oname);
                        $plural = null === $prefix ? $plural : static::prefixKey($plural, $prefix);


                        if (isset($result[$plural]) && is_array($result[$plural])) {
                            $result[$plural][] = $value;
                        } elseif (count($children) !== count($equals)) {
                            $result[$plural][] = $value;
                        } else {
                            $result[$name][] = $value;
                        }
                    }
                } else {
                    $result[$name] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * parseChildNode
     *
     * @param DOMElement $child
     * @param array $result
     *
     * @access private
     * @return mixed
     */
    private static function parseChildNode(DOMElement $child, \DOMNodelist $children, $parentName, array $result = [])
    {
        $prefix = $child->prefix ?: null;
        $nsURL = $child->namespaceURI ?: null;

        $oname = $child->nodeName;
        $name = null === $prefix ? $oname : static::prefixKey($oname, $prefix);

        if ($children->length < 2) {
            $result[$name] = static::parseDomElement($child, $nestedValues);
            return;
        }

        if (isset($result[$name])) {
            if (is_array($result[$name]) && arrayNumeric($result[$name])) {
                $value = static::parseDomElement($child, $nsURL, $prefix);
                if (is_array($value) && arrayNumeric($value)) {
                    $result[$name] = array_merge($result[$name], $value);
                } else {
                    $result[$name][] = $value;
                }
            } else {
                continue;
            }
        } else {

            $equals = static::getEqualNodes($child, $prefix);

            if (1 < $equals->length) {
                if (static::isEqualOrPluralOf($parentName, $oname)) {
                    $result[] = static::parseDomElement($child, $nestedValues);
                } else {
                    $plural = static::pluralize($oname);
                    $plural = is_null($prefix) ? $plural : static::prefixKey($plural, $prefix);
                    if (isset($result[$plural]) && is_array($result[$plural])) {
                        $result[$plural][] = static::parseDomElement($child, $nestedValues);
                    } elseif (count($children) !== count($equals)) {
                        $result[$plural][] = static::parseDomElement($child, $nestedValues);
                    } else {
                        $result[$name][] = static::parseDomElement($child, $nestedValues);
                    }
                }
            } else {
                $result[$name] = $foo = static::parseDomElement($child, $nsURL, $nestedValues);
            }
        }

        return $result;
    }

    private static function isEqualOrPluralOf($name, $singular)
    {
        return $name === $singular || $name === static::pluralize($singular);
    }

    /**
     * pluralize
     *
     * @param mixed $singular
     *
     * @access private
     * @return mixed
     */
    private static function pluralize($singular)
    {
        if (!isset(static::$pluralizer)) {
            return $singular;
        }

        return call_user_func(static::$pluralizer, $singular);
    }

    /**
     * getEqualNodes
     *
     * @param DOMElement $node
     * @param mixed $prefix
     * @access protected
     * @return DOMNodelist
     */
    private static function getEqualNodes(DOMElement $node, $prefix = null)
    {
        $name = is_null($prefix) ? $node->nodeName : sprintf("%s:%s", $prefix, $node->nodeName);
        return $node->xpath(
            sprintf(".|following-sibling::*[name() = '%s']|preceding-sibling::*[name() = '%s']", $name, $name)
        );
    }

    /**
     * prefixKey
     *
     * @param mixed $key
     * @param mixed $prefix
     *
     * @access private
     * @return string
     */
    private static function prefixKey($key, $prefix)
    {
        return sprintf('%s::%s', $prefix, $key);
    }

    /**
     * convertDocument
     *
     * @param \DOMDocument $xml
     *
     * @access private
     * @return mixed
     */
    private static function convertDocument(\DOMDocument $xml)
    {
        $xml = (new XmlLoader)->load($xml->saveXML());
        return $xml;
    }

    /**
     * loadXml
     *
     * @param mixed $xml
     * @param mixed $fromStrig
     * @param string $domClass
     *
     * @access public
     * @return mixed
     */
    public static function loadXml($xml, $fromStrig = false, $domClass = '\Selene\Components\Xml\Dom\DOMDocument')
    {
        $loader = new XmlLoader;

        $loader->setOption('simplexml', false);
        $loader->setOption('from_string', $fromStrig);

        return $loader->load($xml);
    }
}
