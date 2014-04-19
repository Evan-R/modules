<?php

/**
 * This File is part of the Selene\Components\Config\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Xml\Traits\XmlLoaderTrait;

/**
 * @class AbstractXmlLoader
 * @package Selene\Components\Config\Loader
 * @version $Id$
 */
abstract class AbstractXmlLoader extends ConfigLoader
{
    use XmlLoaderTrait {
        XmlLoaderTrait::create as protected createNew;
        XmlLoaderTrait::getErrors as protected getXmlErrors;
        XmlLoaderTrait::load as protected loadXml;
        XmlLoaderTrait::getOption as protected getXmlOption;
        XmlLoaderTrait::setOption as protected setXmlOption;
        XmlLoaderTrait::handleXmlErrors as protected handleXmlRuntimeErrors;
    }

    /**
     * Creates a new Xml configuration loader.
     *
     * @param ContainerInterface $container the DI Container instance.
     *
     * @access public
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->setXmlOption('simplexml', false);
        $this->setXmlOption('from_string', false);
    }
}
