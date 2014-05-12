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
use \Selene\Components\Common\Helper\StringHelper;
use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\Xml\Loader\Loader;
use \Selene\Components\Xml\Loader\LoaderInterface;

/**
 * @class Parser
 * @package Selene\Components\Xml
 * @version $Id$
 */
class Parser implements ParserInterface
{
    use Getter;

    /**
     * pluralizer
     *
     * @var mixed
     */
    private $pluralizer;

    /**
     * keyNormalizer
     *
     * @var callable
     */
    private $keyNormalizer;

    /**
     * options
     *
     * @var array
     */
    private $options;

    /**
     * @param LoaderInterface $loader
     *
     * @access public
     */
    public function __construct(LoaderInterface $loader = null)
    {
        $this->loader = $loader ?: new Loader($this->getLoaderConfig());
        $this->options = [];
    }

    /**
     * setMergeAttributes
     *
     * @param mixed $merge
     *
     * @access public
     * @return mixed
     */
    public function setMergeAttributes($merge)
    {
        return $this->options['merge_attributes'] = (bool)$merge;
    }

    /**
     * setListAttribute
     *
     * @param mixed $attribute
     *
     * @access public
     * @return mixed
     */
    public function setIndexKey($attribute)
    {
        return $this->options['list_key'] = $attribute;
    }

    /**
     * setAttributesKey
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public function setAttributesKey($key)
    {
        $this->options['attribute_key'] = $key;
    }

    /**
     * getAttributesKey
     *
     * @param mixed $key
     *
     * @access public
     * @return string
     */
    public function getAttributesKey()
    {
        return $this->getDefault($this->options, 'attribute_key', '@attributes');
    }

    /**
     * setKeyNormalizer
     *
     * @param callable $normalizer
     *
     * @access public
     * @return void
     */
    public function setKeyNormalizer(callable $normalizer)
    {
        $this->keyNormalizer = $normalizer;
    }

    /**
     * getListKey
     *
     * @access protected
     * @return mixed
     */
    protected function getListKey()
    {
        return $this->getDefault($this->options, 'list_key', null);
    }

    /**
     * isListKey
     *
     * @param mixed $name
     *
     * @access protected
     * @return boolean
     */
    protected function isListKey($name)
    {
        return $this->getDefault($this->options, 'list_key', null) === $name;
    }

    /**
     * mergeAttributes
     *
     * @access protected
     * @return boolean
     */
    protected function mergeAttributes()
    {
        return $this->getDefault($this->options, 'merge_attributes', false);
    }

    /**
     * setPluralizer
     *
     * @param callable $pluralizer
     *
     * @access public
     * @return void
     */
    public function setPluralizer(callable $pluralizer = null)
    {
        $this->pluralizer = $pluralizer;
    }

    /**
     * parse
     *
     * @param DOMDocument $xml
     *
     * @access public
     * @return array
     */
    public function parseDom(\DOMDocument $xml)
    {
        if (!$xml instanceof DOMDocument) {
            $xml = $this->convertDocument($xml);
        }

        if ($root = $xml->documentElement) {
            $children = $this->parseDomElement($root);
            $results = [$xml->documentElement->nodeName => $children];
            return $results;
        }

        throw new \InvalidArgumentException('DOM has no root element');
    }

    /**
     * parse
     *
     * @param mixed $xml
     *
     * @access public
     * @return array
     */
    public function parse($xml)
    {
        $opts = $this->getLoaderConfig();
        $opts['from_string'] = !(is_file($xml) && stream_is_local($xml));

        $dom = $this->loader->load($xml, $opts);

        return $this->parseDom($dom);
    }

    /**
     * parseDomElement
     *
     * @param DOMelement $xml
     *
     * @access public
     * @return null|array
     */
    public function parseDomElement(DOMelement $xml)
    {
        $attributes = $this->parseElementAttributes($xml);

        $hasAttributes = (bool)$attributes;

        $text = $this->prepareTextValue($xml, current($attributes));

        $result = $this->parseElementNodes($xml->xpath('./child::*'), $xml->nodeName);

        if ($hasAttributes) {

            if (null !== $text) {
                $result['value'] = $text;
            }

            if ($this->mergeAttributes()) {
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
     * getPhpValue
     *
     * @param mixed $value
     *
     * @access public
     * @return mixed
     */
    public static function getPhpValue($val, $default = null, ParserInterface $parser = null)
    {
        if ($val instanceof DOMElement) {
            $parser = $parser ?: new static;
            return $parser->parseDomElement($val);
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
    public static function getElementText(\DOMElement $element, $concat = true)
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
     * getLoaderConfig
     *
     * @access protected
     * @return mixed
     */
    protected function getLoaderConfig()
    {
        return [
            'from_string' => false,
            'simplexml' => false,
            'dom_class' => __NAMESPACE__.'\\Dom\DOMDocument'
        ];
    }

    /**
     * normalizeKey
     *
     * @param mixed $key
     *
     * @access protected
     * @return mixed
     */
    protected function normalizeKey($key)
    {
        if (null !== $this->keyNormalizer) {
            return call_user_func($this->keyNormalizer, $key);
        }

        return strtr(StringHelper::strLowDash($key), ['-' => '_']);
    }

    /**
     * convert boolish and numeric values
     *
     * @param mixed $text
     * @param array $attributes
     */
    private function prepareTextValue(DOMElement $xml, $attributes = null)
    {
        $text = static::getElementText($xml, true);
        return (isset($attributes['type']) && 'text' === $attributes['type']) ?
            clearValue($text) :
            static::getPhpValue($text, null, $this);
    }

    /**
     * parseElementNodes
     *
     * @param DOMElement $child
     *
     * @access private
     * @return array
     */
    private function parseElementNodes($children, $parentName = null)
    {
        $result = [];

        foreach ($children as $child) {
            $prefix = $child->prefix ?: null;
            $nsURL = $child->namespaceURI ?: null;

            $oname = $this->normalizeKey($child->nodeName);
            $name = null === $prefix ? $oname : $this->prefixKey($oname, $prefix);

            if (isset($result[$name])) {
                if (is_array($result[$name]) && arrayNumeric($result[$name])) {
                    $value = static::getPhpValue($child, null, $this);

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
                $value = static::getPhpValue($child, null, $this);

                $listKey = $this->getListKey();
                $lKey = $prefix ? $this->prefixKey($listKey, $prefix) : $listKey;

                if (1 < $equals->length || $lKey === $name) {
                    if ($this->isEqualOrPluralOf($parentName, $oname) || $lKey === $name) {
                        $result[] = static::getPhpValue($child, null, $this);
                    } else {
                        $plural = $this->pluralize($oname);
                        $plural = null === $prefix ? $plural : $this->prefixKey($plural, $prefix);


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
     * parseElementAttributes
     *
     * @param DOMelement $xml
     * @param string $attrKey
     *
     * @access private
     * @return mixed
     */
    private function parseElementAttributes(DOMelement $xml)
    {
        $elementAttrs = $xml->xpath('./@*');

        if (0 === $elementAttrs->length) {
            return [];
        }

        $attrs = [];

        foreach ($elementAttrs as $key => $attribute) {

            $namespace = $attribute->namespaceURI;

            $value = static::getPhpValue($attribute->nodeValue, null, $this);

            $name = $this->normalizeKey($attribute->nodeName);

            if ($prefix = $attribute->prefix) {
                $attName = $this->prefixKey($name, $prefix);
            } else {
                $attName  = $name;
            }

            $attrs[$attName] = $value;
        }

        return [$this->getAttributesKey() => $attrs];
    }

    /**
     * isEqualOrPluralOf
     *
     * @param mixed $name
     * @param mixed $singular
     *
     * @access private
     * @return boolean
     */
    private function isEqualOrPluralOf($name, $singular)
    {
        return $name === $singular || $name === $this->pluralize($singular);
    }

    /**
     * pluralize
     *
     * @param mixed $singular
     *
     * @access private
     * @return mixed
     */
    private function pluralize($singular)
    {
        if (!isset($this->pluralizer)) {
            return $singular;
        }

        return call_user_func($this->pluralizer, $singular);
    }

    /**
     * getEqualNodes
     *
     * @param DOMElement $node
     * @param mixed $prefix
     * @access protected
     * @return DOMNodelist
     */
    private function getEqualNodes(DOMElement $node, $prefix = null)
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
    private function prefixKey($key, $prefix)
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
    private function convertDocument(\DOMDocument $xml)
    {
        $xml = $this->loader->load($xml->saveXML(), ['from_string' => true]);
        return $xml;
    }
}
