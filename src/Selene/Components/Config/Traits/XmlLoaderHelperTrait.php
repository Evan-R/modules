<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Traits;

use \Selene\Components\Xml\Parser;
use \Selene\Components\Xml\Normalizer\PhpVarNormalizer;
use \Selene\Components\Xml\Loader\Loader as XmlFileLoader;

/**
 * @trait XmlLoaderHelperTrait
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait XmlLoaderHelperTrait
{
    /**
     * parser
     *
     * @var \Selene\Components\Xml\Parser
     */
    protected $parser;

    /**
     * xmlLoader
     *
     * @var \Selene\Components\Xml\Loader\LoaderInterface
     */
    protected $xmlLoader;

    /**
     * {@inheritdoc}
     * @param string $format
     * @return boolean
     */
    public function supports($resource)
    {
        return is_string($resource) && 'xml' ===  pathinfo(strtolower($resource), PATHINFO_EXTENSION);
    }

    /**
     * Loads an xml file into a DOMDocument.
     *
     * @param string $xml filepath to an xml file
     *
     * @access public
     * @return \Selene\Components\Xml\Dom\DOMDocument
     */
    public function loadXml($xml)
    {
        return $this->getXmlLoader()->load($xml);
    }

    /**
     * Get the XmlLoader instance.
     *
     * @access protected
     * @return \Selene\Components\Xml\Loader\LoaderInterface
     */
    protected function getXmlLoader()
    {
        if (null === $this->xmlLoader) {
            $this->xmlLoader = new XmlFileLoader;
            $this->xmlLoader->setOption('simplexml', false);
            $this->xmlLoader->setOption('from_string', false);
        }

        return $this->xmlLoader;
    }

    /**
     * Get the XmlParser instance.
     *
     * @access protected
     * @return \Selene\Components\Xml\Parser
     */
    protected function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new Parser($this->getXmlLoader());
            $this->parser->setPluralizer(function ($singular) {
                return $singular . 's';
            });
            $this->parser->setMergeAttributes(true);
        }

        return $this->parser;
    }
}
