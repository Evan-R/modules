<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Traits;

use \Selene\Module\Xml\Parser;
use \Selene\Module\Xml\Inflector\SimpleInflector;
use \Selene\Module\Xml\Loader\Loader as XmlFileLoader;

/**
 * @trait XmlLoaderHelperTrait
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
trait XmlLoaderHelperTrait
{
    /**
     * parser
     *
     * @var \Selene\Module\Xml\Parser|null
     */
    protected $parser;

    /**
     * xmlLoader
     *
     * @var \Selene\Module\Xml\Loader\LoaderInterface|null
     */
    protected $xmlLoader;

    /**
     * Loads an xml file into a DOMDocument.
     *
     * @param string $xml filepath to an xml file
     *
     * @return \Selene\Module\Xml\Dom\DOMDocument
     */
    public function loadXml($xml)
    {
        return $this->getXmlLoader()->load($xml);
    }

    /**
     * Get the XmlLoader instance.
     *
     * @return \Selene\Module\Xml\Loader\LoaderInterface
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
     * @return \Selene\Module\Xml\Parser
     */
    protected function getParser()
    {
        if (null === $this->parser) {
            $inflector = new SimpleInflector(true);
            $this->parser = new Parser($this->getXmlLoader());

            $this->parser->setPluralizer([$inflector, 'pluralize']);
            $this->parser->setMergeAttributes(true);
        }

        return $this->parser;
    }
}
