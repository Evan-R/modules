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
use \Selene\Components\Common\Helper\clearValue;
use \Selene\Components\Common\Helper\ListHelper;
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
     * @var callable
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
     * Creates a new `Parser` instance.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader = null)
    {
        $this->loader = $loader ?: new Loader($this->getLoaderConfig());
        $this->options = [];
    }

    /**
     * Toggle on/off merging attributes to array keys.
     *
     * @param boolean $merge
     *
     * @access public
     * @return void
     */
    public function setMergeAttributes($merge)
    {
        $this->options['merge_attributes'] = (bool)$merge;
    }

    /**
     * Set the attributes key name.
     *
     * The default key will be `@attributes`.
     * This will be ignored if merging attributes is active.
     *
     * @param string $key
     *
     * @access public
     * @return void
     */
    public function setAttributesKey($key)
    {
        $this->options['attribute_key'] = $key;
    }

    /**
     * Get the attributes key.
     *
     * Defaults to `@attributes`.
     *
     * @access public
     * @return string
     */
    public function getAttributesKey()
    {
        return $this->getDefault($this->options, 'attribute_key', '@attributes');
    }

    /**
     * Set the list identifier key.
     *
     * Elements that match with that key will always be considered a list,
     * as long as thy have any parent element.
     *
     * @param string $key
     *
     * @access public
     * @return void
     */
    public function setIndexKey($key)
    {
        $this->options['list_key'] = $key;
    }

    /**
     * Set a custom function to normalize an xml node name to a php array key name.
     *
     * By default, hyphens are converted to underscores.
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
     * Set the pluralizer.
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
     * Parses a `\DOMDocument` into an array.
     *
     * @param \DOMDocument $xml
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
     * Parses an xml string or file into an array.
     *
     * @param string $xml
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
     * Parse the contents of a `DOMElement` to an array.
     *
     * @param DOMElement $xml
     *
     * @access public
     * @return null|array
     */
    public function parseDomElement(DOMElement $xml)
    {
        $attributes = $this->parseElementAttributes($xml);

        $hasAttributes = (bool)$attributes;

        $text = $this->prepareTextValue($xml, current($attributes) ?: null);

        $result = $this->parseElementNodes($xml->xpath('./child::*'), $xml->nodeName);

        if ($hasAttributes) {

            if (null !== $text) {
                $result['value'] = $text;
            }

            if ($this->mergeAttributes()) {
                $attributes = $attributes[$this->getAttributesKey()];
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
     * Get the php equivalent of an input value derived from any king of xml.
     *
     * @param mixed $val
     * @param mixed $default
     * @param ParserInterface $parser
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
     * Get the text of a `DOMElement` excluding the contents
     * of its child elements.
     *
     * @param DOMElement $element
     * @param boolean $concat
     *
     * @access private
     * @return string|array returns an array of strings if `$concat` is `false`
     */
    public static function getElementText(DOMElement $element, $concat = true)
    {
        $textNodes = [];

        foreach ($element->xpath('./text()') as $text) {

            if ($value = \clearValue($text->nodeValue)) {
                $textNodes[] = $value;
            }
        }
        return $concat ? implode($textNodes) : $textNodes;
    }


    /**
     * Convert hyphens to underscores.
     *
     * @param string $name
     *
     * @static
     * @access public
     * @return string
     */
    public static function fixNodeName($name)
    {
        return strtr(StringHelper::strLowDash($name), ['-' => '_']);
    }

    /**
     * Get the list identifier key.
     *
     * @access protected
     * @return string
     */
    protected function getListKey()
    {
        return $this->getDefault($this->options, 'list_key', null);
    }

    /**
     * Check if a given string is the list identifier.
     *
     * @param string $name
     * @param string $prefix
     *
     * @access protected
     * @return boolean
     */
    protected function isListKey($name, $prefix = null)
    {
        return $this->prefixKey($this->getListKey(), $prefix) === $name;
    }

    /**
     * Determine weather to merge attributes or not.
     *
     * @access protected
     * @return boolean
     */
    protected function mergeAttributes()
    {
        return $this->getDefault($this->options, 'merge_attributes', false);
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
     * Normalize a node key
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

        return static::fixNodeName($key);
    }

    /**
     * Convert boolean like and numeric values to their php equivalent values.
     *
     * @param DOMElement $xml the element to get the value from
     * @param array $attributes
     * @return mixed
     */
    private function prepareTextValue(DOMElement $xml, array $attributes = null)
    {
        $text = static::getElementText($xml, true);
        return (isset($attributes['type']) && 'text' === $attributes['type']) ?
            clearValue($text) :
            static::getPhpValue($text, null, $this);
    }

    /**
     * Parse a nodelist into a array
     *
     * @param \DOMNodeList|array $children elements to parse
     * @param string $parentName           node name of the parent element
     *
     * @access private
     * @return array
     */
    private function parseElementNodes($children, $parentName = null)
    {
        $result = [];

        foreach ($children as $child) {
            $prefix = $child->prefix ?: null;
            $oname  = $this->normalizeKey($child->nodeName);
            $name   = $this->prefixKey($oname, $prefix);

            if (isset($result[$name])) {
                if (is_array($result[$name]) && ListHelper::arrayIsList($result[$name])) {
                    $value = static::getPhpValue($child, null, $this);

                    if (is_array($value) && ListHelper::arrayIsList($value)) {
                        $result[$name] = array_merge($result[$name], $value);
                    } else {
                        $result[$name][] = $value;
                    }

                } else {
                    continue;
                }
            } else {
                $list = false;
                $equals = $this->getEqualNodes($child, $prefix);
                $value = static::getPhpValue($child, null, $this);

                if (($list = $this->isListKey($name, $prefix) || $this->isEqualOrPluralOf($parentName, $oname))
                    || 1 < $equals->length
                ) {
                    if ($list) {
                        $result[] = $value;
                    } else {
                        $result[$name][] = $value;
                    }
                } else {
                    $result[$name] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * Parse element attributes into an array.
     *
     * @param DOMElement $xml
     *
     * @access private
     * @return array
     */
    private function parseElementAttributes(DOMElement $xml)
    {
        $elementAttrs = $xml->xpath('./@*');

        if (0 === $elementAttrs->length) {
            return [];
        }

        $attrs = [];

        foreach ($elementAttrs as $key => $attribute) {

            $value = static::getPhpValue($attribute->nodeValue, null, $this);

            $name = $this->normalizeKey($attribute->nodeName);

            $attrs[$this->prefixKey($name, $attribute->prefix ?: null)] = $value;
        }

        return [$this->getAttributesKey() => $attrs];
    }

    /**
     * Check if the input string is a plural or equal to a given comparative string.
     *
     * @param string $name the input string
     * @param string $singular the string to compare with
     *
     * @access private
     * @return boolean
     */
    private function isEqualOrPluralOf($name, $singular)
    {
        return $name === $singular || $name === $this->pluralize($singular);
    }

    /**
     * Attempt to pluralize a string.
     *
     * @param string $singular
     *
     * @access private
     * @return string
     */
    private function pluralize($singular)
    {
        if (null === $this->pluralizer) {
            return $singular;
        }

        return call_user_func($this->pluralizer, $singular);
    }

    /**
     * A lookahead to find sibling elements with similar names.
     *
     * @param DOMElement $node the node in charge.
     * @param string     $prefix the element prefix
     *
     * @access protected
     * @return \DOMNodeList
     */
    private function getEqualNodes(DOMElement $node, $prefix = null)
    {
        $name = is_null($prefix) ? $node->nodeName : sprintf("%s:%s", $prefix, $node->nodeName);
        return $node->xpath(
            sprintf(".|following-sibling::*[name() = '%s']|preceding-sibling::*[name() = '%s']", $name, $name)
        );
    }

    /**
     * Prepend a string.
     *
     * Will pass-through the original string if `$prefix` is `null`.
     *
     * @param string $key the key to prefix
     * @param string $prefix the prefix
     *
     * @access private
     * @return string
     */
    private function prefixKey($key, $prefix = null)
    {
        return $prefix ? sprintf('%s::%s', $prefix, $key) : $key;
    }

    /**
     * Converts a `\DOMDocument`that is not an instance of
     * `Selene\Components\Dom\DOMDocument`.
     *
     * @param \DOMDocument $xml the document to convert
     *
     * @access private
     * @return DOMDocument
     */
    private function convertDocument(\DOMDocument $xml)
    {
        $xml = $this->loader->load($xml->saveXML(), ['from_string' => true]);
        return $xml;
    }
}
