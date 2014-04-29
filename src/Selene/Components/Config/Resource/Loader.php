<?php

/**
 * This File is part of the Selene\Components\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @class Loader
 * @package Selene\Components\Config\Resource
 * @version $Id$
 */
abstract class Loader implements LoaderInterface
{
    protected $locator;

    protected $resolver;

    protected $resourcePath;

    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    public function getResolver()
    {
        return $this->resolver;
    }

    public function getResourcePath()
    {
        return $this->resourcePath;
    }

    public function setResourcePath($path)
    {
        $this->resourcePath = $path;
    }

    /**
     * import
     *
     * @param mixed $resource
     *
     * @access public
     * @return void
     */
    public function import($resource)
    {
        if ($this->supports($resource)) {
            return $this->load($resource);
        }


        if ($resolver = $this->getResolver() && $loader = $this->getResolver()->resolve($resource)) {
            $loader->load($resource);
        }
    }

    abstract public function load($resource);

    abstract public function supports($resource);
}
