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

use \Selene\Components\Xml\Dom\DOMDocument;

/**
 * @class XmlParser
 *
 * @package Selene\Components\Xml
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class XmlParser
{
    /**
     * dom
     *
     * @var DOMDOcument
     */
    protected $dom;

    /**
     * pluralizer
     *
     * @var callable
     */
    protected $pluralizer;

    /**
     * @param XmlLoader $loader
     * @param callable $pluralizer
     *
     * @access public
     */
    public function __construct(XmlLoader $loader = null, callable $pluralizer = null)
    {
        $this->loader     = $loader ?: new XmlLoader;
        $this->pluralizer = $pluralizer;
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
     * load
     *
     * @param mixed $file
     *
     * @access public
     * @return void
     */
    public function load($file)
    {
        $from_string = $this->loader->getOption('from_string');
        $simplexml   = $this->loader->getOption('simplexml');

        $this->loader->setOption('from_string', true);
        $this->loader->setOption('simplexml', false);

        $this->dom = $this->loader->load($file);

        $this->loader->setOption('from_string', $from_string);
        $this->loader->setOption('simplexml', $simplexml);
    }

    /**
     * loadString
     *
     * @param mixed $xml
     *
     * @access public
     * @return void
     */
    public function loadString($xml)
    {
        $from_string = $this->loader->getOption('from_string');
        $simplexml   = $this->loader->getOption('simplexml');

        $this->loader->setOption('from_string', true);
        $this->loader->setOption('simplexml', false);

        $this->dom = $this->loader->load($xml);

        $this->loader->setOption('from_string', $from_string);
        $this->loader->setOption('simplexml', $simplexml);
    }

    /**
     * parse
     *
     * @access public
     * @return mixed
     */
    public function parse()
    {
        if (!$this->dom) {
            throw new \BadMethodCallException('No xml file or string loaded');
        }
        return $this->doParse($this->dom);
    }

    /**
     * parseDocument
     *
     * @param DOMDocument $xml
     *
     * @access public
     * @return mixed
     */
    public function parseDocument(DOMDocument $xml)
    {
        return $this->doParse($xml);
    }

    /**
     * doParse
     *
     * @param DOMDocument $xml
     *
     * @access protected
     * @return mixed
     */
    protected function doParse(DOMDocument $xml)
    {
        Parser::setPluralizer($this->pluralizer);

        $result = Parser::parse($xml);

        Parser::setPluralizer(null);

        return $result;
    }
}
