<?php

/**
 * This File is part of the Selene\Components\Routing\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader;

use \Selene\Components\Xml\Dom\DOMElement;
use \Selene\Components\Xml\Dom\DOMDocument;
use \Selene\Components\DI\Loader\ConfigLoader;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Xml\Builder;
use \Selene\Components\Xml\Traits\XmlLoaderTrait;

/**
 * @class XmlLoader
 * @package Selene\Components\Routing\Loader
 * @version $Id$
 */
class XmlLoader extends BaseXmlLoader
{
    use XmlLoaderTrait {
        XmlLoaderTrait::create as private createNew;
        XmlLoaderTrait::getErrors as private getXmlErrors;
        XmlLoaderTrait::load as private loadXml;
        XmlLoaderTrait::getOption as private getXmlOption;
        XmlLoaderTrait::setOption as private setXmlOption;
        XmlLoaderTrait::handleXmlErrors as private handleXmlRuntimeErrors;
    }
}
