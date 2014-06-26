<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package\Dumper;

use \Selene\Components\Xml\Writer as XmlWriter;

/**
 * @interface ConfigDumperInterface
 * @package Selene\Components\Package
 * @version $Id$
 */
class XmlConfigDumper implements ConfigDumperInterface
{
    public function __construct(XmlWriter $writer = null)
    {
        $this->xmlWriter = $writer;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return 'config.xml';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return 'xml' === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($name, array $contents = [], $format = null)
    {
        if (!$this->supports($format)) {
            return;
        }

        $dom = $this->getXmlWriter()->writeToDom($contents, 'config');
        $dom->firstChild->setAttribute('package', $name);


        return $dom->saveXML(null, empty($contents) ? LIBXML_NOEMPTYTAG : null);
    }

    /**
     * setXmlWriter
     *
     * @param XmlWriter $writer
     *
     * @access public
     * @return mixed
     */
    public function setXmlWriter(XmlWriter $writer)
    {
        $this->xmlWriter = $writer;
    }

    /**
     * getXmlWriter
     *
     *
     * @access protected
     * @return mixed
     */
    protected function getXmlWriter()
    {
        if (null === $this->xmlWriter) {
            $this->xmlWriter = new XmlWriter;
            $this->xmlWriter->setInflector($this->getWriterInflector());
        }

        return $this->xmlWriter;
    }

    private function getWriterInflector()
    {
        return function ($string) {
            $len = strlen($string);

            if (0 === $len) {
                return $string;
            }

            if ('s' !== $string[$len - 1]) {
                return $string;
            }

            return substr($string, 0, $len - 1);
        };
    }
}
