<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

use \Selene\Components\Common\Traits\Getter;
use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\Loader\PhpLoader;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\DI\Loader\CallableLoader;
use \Selene\Components\Config\Configuration;
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\Config\Resource\LoaderResolver;
use \Selene\Components\Config\Resource\LocatorInterface;
use \Selene\Components\Config\Resource\DelegatingLoader;
use \Selene\Components\Routing\Loader\PhpLoader as PhpRoutingLoader;
use \Selene\Components\Routing\Loader\XmlLoader as XmlRoutingLoader;
use \Selene\Components\Routing\Loader\CallableLoader as CallableRoutingLoader;

/**
 * @abstract class PackageConfiguration extends BaseConfig
 * @see BaseConfig
 * @abstract
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class PackageConfiguration extends Configuration
{
    use Getter;

    /**
     * package
     *
     * @var mixed
     */
    private $packagePath;

    public function __construct($packagePath)
    {
        $this->packagePath = $packagePath;
    }

    /**
     * getRoutingLoader
     *
     * @param mixed $param
     *
     * @access protected
     * @return LoaderInterface
     */
    protected function getRoutingLoader(BuilderInterface $builder, LocatorInterface $locator = null)
    {
        $locator = $locator ?: new Locator([$this->getResourcePath()]);

        return new DelegatingLoader(new LoaderResolver([
            new CallableRoutingLoader($builder, $locator),
            new PhpRoutingLoader($builder, $locator),
            new XmlRoutingLoader($builder, $locator)
        ]));
    }

    /**
     * getConfigLoader
     *
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     *
     * @access protected
     * @return LoaderInterface
     */
    protected function getConfigLoader(BuilderInterface $builder, LocatorInterface $locator = null)
    {
        $locator = $locator ?: new Locator([$this->getResourcePath()]);

        return new DelegatingLoader(new LoaderResolver([
            new CallableLoader($builder, $locator),
            new PhpLoader($builder, $locator),
            new XmlLoader($builder, $locator)
        ]));
    }

    /**
     * getResourcePath
     *
     * @access protected
     * @return string
     */
    protected function getResourcePath()
    {
        return $this->getPackagePath().DIRECTORY_SEPARATOR.'Resources'.DIRECTORY_SEPARATOR.'config';
    }

    /**
     * getPackage
     *
     * @access protected
     * @return string
     */
    protected function getPackagePath()
    {
        return $this->packagePath;
    }
}
