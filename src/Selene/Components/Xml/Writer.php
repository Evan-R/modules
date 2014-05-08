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

use \Selene\Components\Xml\DOM\DOMElement;
use \Selene\Components\Xml\DOM\DOMDocument;
use \Selene\Components\Xml\Traits\XmlHelperTrait;
use \Selene\Components\Xml\Normalizer\Normalizer;
use \Selene\Components\Xml\Normalizer\NormalizerInterface;
use \Selene\Components\Common\Helper\ListHelper;

/**
 * @class Writer
 *
 * @package Selene\Components\Xml
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Writer
{
    use XmlHelperTrait;

    /**
     * dom
     *
     * @var mixed
     */
    protected $dom;

    /**
     * encoding
     *
     * @var string
     */
    protected $encoding;

    /**
     * normalizer
     *
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * inflector
     *
     * @var callable
     */
    protected $inflector;

    /**
     * attributemap
     *
     * @var mixed
     */
    protected $attributemap;

    /**
     * nodeValueKey
     *
     * @var string
     */
    protected $nodeValueKey;

    /**
     * indexKey
     *
     * @var mixed
     */
    protected $indexKey;

    /**
     * @param String $normalizer
     * @param NormalizerInterface $normalizer
     *
     * @access public
     */
    public function __construct(NormalizerInterface $normalizer = null, $encoding = 'UTF-8')
    {
        $this->setNormalizer($normalizer ?: new Normalizer);
        $this->setEncoding($encoding);

        $this->attributemap = [];
        $this->indexKey = 'item';
    }

    /**
     * Dump the input data to a xml string.
     *
     * @access public
     * @return string
     */
    public function dump($data, $rootName = 'root')
    {
        $dom = $this->writeToDom($data, $rootName);
        return $dom->saveXML();
    }

    /**
     * Write the input data to a DOMDocument
     *
     * @param mixed $data
     * @param string $rootName
     *
     * @access public
     * @return DOMDocument
     */
    public function writeToDom($data, $rootName = 'root')
    {
        $dom = new DOMDocument('1.0', $this->getEncoding());

        $root = $dom->createElement($rootName);

        $this->buildXML($dom, $root, $data);
        $dom->appendChild($root);

        return $dom;
    }

    /**
     * setNormalizer
     *
     * @param NormalizerInterface $normalizer
     * @access public
     * @return void
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * setInflector
     *
     * @param callable $inflector
     *
     * @access public
     * @return void
     */
    public function setInflector(callable $inflector)
    {
        $this->inflector = $inflector;
    }

    /**
     * setEncoding
     *
     * @param mixed $encoding
     * @access public
     * @return void
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
    }

    /**
     * setAttributeMapp
     *
     * @param array $map
     * @access public
     * @return void
     */
    public function setAttributeMap(array $map)
    {
        $this->attributemap = $map;
    }

    /**
     * useKeyAsValue
     *
     * @param mixed $key
     *
     * @access public
     * @return void
     */
    public function useKeyAsValue($key, $normalize = false)
    {
        if (true !== $normalize and !$this->isValidNodeName($key)) {
            throw new \InvalidArgumentException(sprintf('%s is an invalid node name', $key));
        } else {
            $key = $this->normalizer->normalize($key);
        }

        $this->nodeValueKey = $key;
    }

    /**
     * setIndexKey
     *
     * @param string $key
     * @access public
     * @return void
     */
    public function useKeyAsIndex($key, $normalize = false)
    {
        if (true !== $normalize and !$this->isValidNodeName($key)) {
            throw new \InvalidArgumentException(sprintf('%s is an invalid node name', $key));
        } else {
            $key = $this->normalizer->normalize($key);
        }

        return $this->indexKey = $key;
    }

    /**
     * addMappedAttribute
     *
     * @param mixed $nodeName
     * @param mixed $attribute
     *
     * @access public
     * @return mixed
     */
    public function addMappedAttribute($nodeName, $attribute)
    {
        $this->attributemap[$nodeName][] = $attribute;
    }

    /**
     * getEncoding
     *
     * @access public
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * getNormalizer
     *
     * @access public
     * @return Thapp\XmlBuilder\NormalizerInterface
     */
    public function getNormalizer()
    {
        return $this->normalizer;
    }

    /**
     * buildXML
     *
     * @param DOMNode $DOMNode
     * @param mixed $data
     * @access protected
     * @return void
     */
    protected function buildXML(DOMDocument $dom, \DOMNode &$DOMNode, $data)
    {
        $normalizer = $this->getNormalizer();

        if (null === $data) {
            return;
        }

        if (ListHelper::isTraversable($data) && !$this->isXmlElement($data)) {
            $this->buildXmlFromTraversable($dom, $DOMNode, $normalizer->ensureBuildable($data));
        } else {
            $this->setElementValue($dom, $DOMNode, $data);
        }
    }

    /**
     * buildXmlFromTraversable
     *
     * @param DOMNode $DOMNode
     * @param mixed $data
     * @param NormalizerInterface $normalizer
     * @param mixed $ignoreObjects
     * @access protected
     * @return void
     */
    protected function buildXmlFromTraversable(\DOMDocument $dom, \DOMNode $DOMNode, $data)
    {
        $normalizer = $this->getNormalizer();
        $isIndexedArray = ListHelper::arrayIsList($data);
        $hasAttributes = false;

        foreach ($data as $key => $value) {

            if (!is_scalar($value)) {

                if (!$value = $normalizer->ensureBuildable($value)) {
                    continue;
                }
            }

            if ($this->mapAttributes($DOMNode, $normalizer->normalize($key), $value)) {
                $hasAttributes = true;
                continue;
            }

            // set the default index key if there's no other way:
            if (is_int($key) || !$this->isValidNodeName($key)) {
                $key = $this->indexKey;
            }

            if (is_array($value) && !is_int($key)) {


                if (ListHelper::arrayIsList($value)) {
                    if (($skey = $this->inflect($key)) && ($key !== $skey)) {
                        $parentNode = $dom->createElement($key);
                        foreach ($value as $arrayValue) {
                            $this->appendDOMNode($dom, $parentNode, $skey, $arrayValue);
                        }
                        $DOMNode->appendChild($parentNode);
                    } elseif (null !== $this->indexKey) {
                        $parentNode = $dom->createElement($key);
                        $this->buildXmlFromTraversable($dom, $parentNode, $value);
                        $DOMNode->appendChild($parentNode);
                    } else {
                        foreach ($value as $arrayValue) {
                            $this->appendDOMNode(
                                $dom,
                                $DOMNode,
                                $this->inflect($normalizer->normalize($key)),
                                $arrayValue
                            );
                        }
                    }
                    continue;
                }
            } elseif ($this->isXMLElement($value)) {
                // if this is a non scalar value at this time, just set the
                // value on the element
                $node = $dom->createElement($normalizer->normalize($key));
                $DOMNode->appendChild($node);
                $this->setElementValue($dom, $node, $value);
                continue;
            }

            if ($this->isValidNodeName($key)) {
                $this->appendDOMNode($dom, $DOMNode, $normalizer->normalize($key), $value, $hasAttributes);
            }
        }
    }

    /**
     * appendDOMNode
     *
     * @param DOMNode $DOMNode
     * @param string  $name
     * @param mixed   $value
     * @param boolean $hasAttributes
     * @access protected
     * @return void
     */
    protected function appendDOMNode(\DOMDocument $dom, $DOMNode, $name, $value = null, $hasAttributes = false)
    {
        $element = $dom->createElement($name);

        if ($hasAttributes && $this->nodeValueKey === $name) {
            $this->setElementValue($dom, $DOMNode, $value);
        } elseif ($this->setElementValue($dom, $element, $value)) {
            $DOMNode->appendChild($element);
        }
    }

    /**
     * inflect
     *
     * @param mixed $string
     *
     * @access protected
     * @return string
     */
    protected function inflect($string)
    {
        if (null === $this->inflector) {
            return $string;
        }

        return call_user_func($this->inflector, $string);
    }

    /**
     * mapAttributes
     *
     * @access protected
     * @return boolean
     */
    protected function mapAttributes(\DOMNode &$DOMNode, $key, $value)
    {
        if ($attrName = $this->isAttribute($DOMNode, $key)) {

            if (is_array($value)) {
                foreach ($value as $attrKey => $attrValue) {
                    $DOMNode->setAttribute($attrKey, $this->getValue($attrValue));
                }
            } else {
                $DOMNode->setAttribute($attrName, $this->getValue($value));
            }
            return true;
        }
        return false;
    }

    /**
     * isAttribute
     *
     * @param DOMNode $parent
     * @param mixed $key
     * @access protected
     * @return string|boolean
     */
    protected function isAttribute(\DOMNode $parent, $key)
    {
        if (0 === strpos($key, '@') && $this->isValidNodeName($attrName = substr($key, 1))) {
            return $attrName;
        }

        if ($this->isMappedAttribute($parent->nodeName, $key) && $this->isValidNodeName($key)) {
            return $key;
        }
        return false;
    }

    /**
     * isMappedAttribute
     *
     * @param mixed $name
     * @param mixed $key
     * @access public
     * @return boolean
     */
    public function isMappedAttribute($name, $key)
    {
        $map = isset($this->attributemap[$name]) ? $this->attributemap[$name] : [];

        if (isset($this->attributemap['*'])) {
            $map = array_merge($this->attributemap['*'], $map);
        }

        return in_array($key, $map);
    }

    /**
     * setElementValue
     *
     * @param DOMNode $DOMNode
     * @param mixed $value
     */
    protected function setElementValue(DOMDocument $dom, DOMElement $DOMNode, $value = null)
    {
        switch (true) {
            case $value instanceof \SimpleXMLElement:
                $node = dom_import_simplexml($value);
                $node = $dom->importNode($node, true);
                $DOMNode->appendChild($node);
                break;
            case $value instanceof \DOMDocument:
                $DOMNode->appendDomElement($value->firstChild);
                break;
            case $value instanceof \DOMElement:
                $dom->appendDomElement($value, $DOMNode);
                break;
            case is_array($value) || $value instanceof \Traversable:
                $this->buildXML($dom, $DOMNode, $value);
                return true;
            case is_numeric($value):
                if (is_string($value)) {
                    return $this->createTextNodeWithTypeAttribute($dom, $DOMNode, (string)$value, 'string');
                }
                return $this->createText($dom, $DOMNode, (string)$value);
            case is_bool($value):
                return $this->createText($dom, $DOMNode, $value ? 'yes' : 'no');
            case is_string($value):
                if (preg_match('/(<|>|&)/i', $value)) {
                    return $this->createCDATASection($dom, $DOMNode, $value);
                }
                return $this->createText($dom, $DOMNode, $value);
            default:
                return $value;
        }
    }

    /**
     * createText
     *
     * @param DOMNode $DOMNode
     * @param string  $value
     * @access protected
     * @return boolean
     */
    protected function createText(\DOMDocument $dom, \DOMNode $DOMNode, $value)
    {
        $text = $dom->createTextNode($value);
        $DOMNode->appendChild($text);
        return true;
    }

    /**
     * createCDATASection
     *
     * @param DOMNode $DOMNode
     * @param string  $value
     * @access protected
     * @return boolean
     */
    protected function createCDATASection(\DOMDocument $dom, \DOMNode $DOMNode, $value)
    {
        $cdata = $dom->createCDATASection($value);
        $DOMNode->appendChild($cdata);

        return true;
    }

    /**
     * createTextNodeWithTypeAttribute
     *
     * @param DOMNode $DOMNode
     * @param mixed   $value
     * @param string  $type
     * @access protected
     * @return boolean
     */
    protected function createTextNodeWithTypeAttribute(\DOMDocument $dom, \DOMNode $DOMNode, $value, $type = 'int')
    {
        $text = $dom->createTextNode($value);
        $attr = $dom->createAttribute('type');
        $attr->value = $type;
        $DOMNode->appendChild($text);
        $DOMNode->appendChild($attr);

        return true;
    }

    /**
     * getValue
     *
     * @param mixed $value
     *
     * @access protected
     * @return mixed
     */
    protected function getValue($value)
    {
        switch (true) {
            case is_bool($value):
                return $value ? 'true' : 'false';
            case is_numeric($value):
                return ctype_digit($value) ? intval($value) : floatval($value);
            case in_array($value, ['true', 'false']):
                return ('false' === $value || 'no' === $value) ? false : true;
            default:
                return clear_value(trim($value));
        }
    }

    /**
     * isValidNodeName
     *
     * @param mixed $name
     * @access protected
     * @return boolean
     */
    protected function isValidNodeName($name)
    {
        return strlen($name) > 0 && false === strpos($name, ' ') && preg_match('#^[\pL_][\pL0-9._-]*$#ui', $name);
    }
}
