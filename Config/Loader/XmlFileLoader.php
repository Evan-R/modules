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

use \Selene\Components\Config\Traits\XmlLoaderHelperTrait;

/**
 * @class XmlLoader
 * @package Selene\Components\Config\Loader
 * @version $Id$
 */
abstract class XmlFileLoader extends FileLoader
{
    use XmlLoaderHelperTrait;

    /**
     * extension
     *
     * @var string
     */
    protected $extension = 'xml';
}