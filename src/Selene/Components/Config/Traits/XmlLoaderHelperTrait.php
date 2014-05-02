<?php

/**
 * This File is part of the Selene\Components\Config\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Traits;

use \Selene\Components\Xml\Parser;
use \Selene\Components\Xml\Loader\Loader as XmlFileLoader;

/**
 * @class XmlLoaderHelperTrait
 * @package Selene\Components\Config\Traits
 * @version $Id$
 */
trait XmlLoaderHelperTrait
{
    protected $parser;

    protected $xmlLoader;

    /**
     * {@inheritdoc}
     * @param string $format
     */
    public function supports($resource)
    {
        return is_string($resource) && 'xml' ===  pathinfo(strtolower($resource), PATHINFO_EXTENSION);
    }

    /**
     * loadXml
     *
     * @param mixed $xml
     *
     * @access public
     * @return mixed
     */
    public function loadXml($xml)
    {
        return $this->getXmlLoader()->load($xml);
    }

    /**
     * getXmlLoader
     *
     * @access protected
     * @return XmlLoader
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
     * getParser
     *
     * @access protected
     * @return Parser
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
