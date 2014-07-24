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
     * Constructor.
     *
     * @param LocatorInterface $locator
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
     * @return void
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * getResolver
     *
     * @return LoaderResolverInterface
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * getResourcePath
     *
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

    /**
     * load
     *
     * @param mixed   $resource
     * @param boolean $any
     *
     * @return void
     */
    public function load($resource, $any = false)
    {
        $resources = [];

        if ($any) {
            foreach ($this->locator->locate($resource, true) as $file) {
                $this->doLoad($file);
            }
        } else {
            $this->loadResource($this->locator->locate($resource));
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract public function supports($resource);

    /**
     * doLoad
     *
     * @param mixed $resource
     *
     * @return void
     */
    abstract protected function doLoad($resource);

    /**
     * notifyResource
     *
     * @param mixed $resource
     *
     * @return void
     */
    abstract protected function notifyResource($resource);

    private function loadResource($resource)
    {
        $this->notifyResource($resource);
        $this->doLoad($resource);
    }
}
