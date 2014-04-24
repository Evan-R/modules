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

/**
 * @class Builder
 * @package Selene\Components\Xml
 * @version $Id$
 */
class Builder
{
    /**
     * buildXML
     *
     * @param DOMNode $DOMNode
     * @param mixed $data
     * @access protected
     * @return void
     */
    protected function buildXML(DOMNode &$DOMNode, $data)
    {
        $normalizer = $this->getNormalizer();

        if (is_null($data)) {
            return;
        }

        if ($normalizer->isTraversable($data) and !$normalizer->isXMLElement($data)) {
            $this->buildXmlFromTraversable($DOMNode, $normalizer->ensureBuildable($data), $normalizer);
        } else {
            $this->setElementValue($DOMNode, $data);
        }
    }

    /**
     * parseXML
     *
     * @param SimpleXMLElement $xml
     * @access protected
     * @return array
     */
    public static function parseXML(SimpleXMLElement $xml, $nestedValues = true)
    {
        $childpool  = $xml->xpath('child::*');
        $attributes = $xml->xpath('./@*');
        $parentName = $xml->getName();

        if (!empty($attributes)) {
            $attrs = array();
            foreach ($attributes as $key => $attribute) {
                $namespaces = $attribute->getnameSpaces();
                $value = $this->getValue((string)$attribute);
                if ($prefix = $this->nsPrefix($namespaces)) {
                    $attName = $this->prefixKey($prefix, $attribute->getName());
                } else {
                    $attName  = $attribute->getName();
                }
                $attrs[$attName] = $value;
            }
            $attributes = array('@attributes' => $attrs);
        }

        $text = $this->prepareTextValue($xml, current($attributes));
        $result = $this->childNodesToArray($childpool, $parentName);

        if (!empty($attributes)) {
            if (!is_null($text)) {
                $result[$this->getTypeKey($text)] = $text;
            }
            $result = array_merge($attributes, $result);
            return $result;

        } elseif (!is_null($text)) {
            if (!empty($result)) {
                $result[$this->getTypeKey($text)] = $text;
            } else {
                $result = $text;
            }
            return $result;
        }
        return (empty($result) && is_null($text)) ? null : $result;
    }

    /**
     * childNodesToArray
     *
     * @param array $children array containing SimpleXMLElements, most likely
     *  derived from an xpath query
     * @param string $parentName local-name of the parent node
     * @access public
     * @return array
     */
    public static function childNodesToArray($children, $parentName = null, $nestedValues = false)
    {
        $result = array();
        foreach ($children as $child) {

            if (!static::isSimpleXMLElement($child)) {
                throw new InvalidArgumentException(
                    sprintf('The input array must only contain SimpleXMLElements but contains %s', gettype($child))
                );
            }

            $localNamespaces = $child->getNamespaces();
            $prefix = key($localNamespaces);
            $prefix = strlen($prefix) ? $prefix : null;
            $nsURL = current($localNamespaces);

            $name = $child->getName();
            $oname = $name;
            $name = is_null($prefix) ? $name : static::prefixKey($prefix, $name);

            if (count($children) < 2) {
                $result[$name] = static::parseXML($child, $nestedValues);
                break;
            }

            if (isset($result[$name])) {
                if (is_array($result[$name]) && arrayNumeric($result[$name])) {
                    $value = static::parseXML($child, $nsURL, $prefix);
                    if (is_array($value) && arrayNumeric($value)) {
                        $result[$name] = array_merge($result[$name], $value);
                    } else {
                        $result[$name][] = $value;
                    }
                } else {
                    continue;
                }
            } else {

                $equals = $this->getEqualNodes($child, $prefix);

                if (count($equals) > 1) {
                    if (static::isEqualOrPluralOf($parentName, $oname)) {
                        $result[] = static::parseXML($child, $nestedValues);
                    } else {
                        $plural = static::pluralize($oname);
                        $plural = is_null($prefix) ? $plural : $this->prefixKey($prefix, $plural);
                        if (isset($result[$plural]) && is_array($result[$plural])) {
                            $result[$plural][] = static::parseXML($child, $nestedValues);
                        } elseif (count($children) !== count($equals)) {
                            $result[$plural][] = static::parseXML($child, $nestedValues);
                        } else {
                            $result[$name][] = static::parseXML($child, $nestedValues);
                        }
                    }
                } else {
                    $result[$name] = static::parseXML($child, $nsURL, $nestedValues);
                }
            }
        }
        return $result;
    }

    /**
     * prefixKey
     *
     * @param mixed $prefix
     * @param mixed $localName
     *
     * @access private
     * @return mixed
     */
    private static function prefixKey($prefix, $localName, $prefixSeparator = ':', $checkPrefixes = false)
    {
        if (!$checkPrefixes) {
            return $localName;
        }
        return sprintf('%s%s%s', $prefix, $prefixSeparator, $localName);
    }

    /**
     * isSimpleXMLElement
     *
     * @param mixed $element
     * @access public
     * @return mixed
     */
    private static function isSimpleXMLElement($element)
    {
        return $element instanceof \SimpleXMLElement;
    }

    /**
     * isEqualOrPluralOf
     *
     * @param mixed $name
     * @param mixed $singular
     * @access protected
     * @return boolean
     */
    private static function isEqualOrPluralOf($name, $singular)
    {
        return $name === $singular || $name === static::pluralize($singular);
    }

    private static function pluralize($singular)
    {
        if (!isset(static::$pluralizer)) {
            return $singular;
        }

        return static::$pluralizer->pluralize($singular);
    }

    /**
     * getEqualNodes
     *
     * @param SimpleXMLElement $node
     * @param mixed $prefix
     * @access protected
     * @return array
     */
    private static function getEqualNodes(SimpleXMLElement $node, $prefix = null)
    {
        $name = is_null($prefix) ? $node->getName() : sprintf("%s:%s", $prefix, $node->getName());
        return $node->xpath(
            sprintf(".|following-sibling::*[name() = '%s']|preceding-sibling::*[name() = '%s']", $name, $name)
        );
    }

    /**
     * getEqualFollowingNodes
     *
     * @param SimpleXMLElement $node
     * @param mixed $prefix
     * @access protected
     * @return array
     */
    private static function getEqualFollowingNodes(SimpleXMLElement $node, $prefix = null)
    {
        $name = is_null($prefix) ? $node->getName() : sprintf("%s:%s", $prefix, $node->getName());
        return $node->xpath(
            sprintf(".|following-sibling::*[name() = '%s']", $name)
        );
    }

    /**
     * simpleXMLParentElement
     *
     * @param SimpleXMLElement $element
     * @param int $maxDepth
     * @access protected
     * @return boolean|SimpleXMLElement
     */
    private static function simpleXMLParentElement(SimpleXMLElement $element, $maxDepth = 4)
    {
        if (!$parent = current($element->xpath('parent::*'))) {
            $xpath = '';
            while ($maxDepth--) {
                $xpath .= '../';
                $query = sprintf('%sparent::*', $xpath);

                if ($parent = current($element->xpath($query))) {
                    return $parent;
                }

            }
        }
        return $parent;
    }

    /**
     * nsPrefix
     *
     * @param array $namespaces
     * @access protected
     * @return mixed
     */
    private static function nsPrefix(array $namespaces)
    {
        $prefix = key($namespaces);
        return strlen($prefix) ? $prefix : null;
    }
    /**
     * convert boolish and numeric values
     *
     * @param mixed $text
     * @param array $attributes
     */
    private static function prepareTextValue(SimpleXMLElement $xml, $attributes = null)
    {
        return (isset($attributes['type']) && 'text' === $attributes['type']) ?
            clearValue((string)$xml) :
            static::getValue((string)$xml);
    }

    /**
     * determine the array key name for textnodes with attributes
     *
     * @param mixed|string $value
     */
    private static function getTypeKey($value)
    {
        //return is_string($value) ? 'text' : 'value';
        return $this->nodeValueKey;
    }

    /**
     * getValue
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public static function getValue($value)
    {
        switch (true) {
            case is_bool($value):
                return $value ? 'true' : 'false';
            case is_numeric($value):
                return ctype_digit($value) ? intval($value) : floatval($value);
            case in_array($value, array('true', 'false', 'yes', 'no')):
                return ('false' === $value || 'no' === $value) ? false : true;
            default:
                return clearValue(trim($value));
        };
    }

    /**
     * getPhpValue
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public static function getPhpValue($val, $default = null)
    {
        if (0 === strlen($val)) {
            return $default;
        } elseif (is_numeric($val)) {
            return false !== strpos($val, '.') ? floatVal($val) : intVal($val);
        } elseif (($lval = strtolower($val)) === 'true' || $lval === 'false') {
            return $lval === 'true' ? true : false;
        }

        return $val;
    }
}
