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
 * @abstract class Loader implements LoaderInterface
 * @see LoaderInterface
 * @abstract
 *
 * @package \Users\malcolm\www\selene_source\src\Selene\Components\Config\Resource
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Loader implements LoaderInterface
{
    /**
     * locator
     *
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * resolver
     *
     * @var LoaderResolverInterface
     */
    protected $resolver;

    /**
     * resourcePath
     *
     * @var string
     */
    protected $resourcePath;

    /**
     * @param LocatorInterface $locator
     *
     * @access public
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * setResolver
     *
     * @param LoaderResolverInterface $resolver
     *
     * @access public
     * @return void
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * getResolver
     *
     * @access public
     * @return LoaderResolverInterface
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * getResourcePath
     *
     * @access public
     * @return string
     */
    public function getResourcePath()
    {
        return $this->resourcePath;
    }

    /**
     * setResourcePath
     *
     * @param string $path
     *
     * @access public
     * @return void
     */
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

    public function load($resource, $any = false)
    {
        if ($any) {
            foreach ($this->locator->locate($resource, true) as $file) {
                $this->doLoad($file);
            }
        } else {
            $this->doLoad($this->locator->locate($resource));
        }
    }

    abstract public function supports($resource);

    abstract protected function doLoad($file);
}
