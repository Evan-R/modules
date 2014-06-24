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
 * @class LoaderResolver
 * @package Selene\Components\Config\Resource
 * @version $Id$
 */
class LoaderResolver implements LoaderResolverInterface
{
    private $loaders;

    public function __construct(array $loaders = [])
    {
        $this->loaders = [];
        $this->setLoaders($loaders);
    }

    /**
     * addLoader
     *
     * @param LoaderInterface $loader
     *
     * @access public
     * @return mixed
     */
    public function addLoader(LoaderInterface $loader)
    {
        $loader->setResolver($this);
        $this->loaders[] = $loader;
    }

    public function setLoaders(array $loaders)
    {
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    public function resolve($resource)
    {
        foreach ($this->loaders as $loader) {

            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        $error = is_object($resource) ? get_class($resource) :
            (is_string($resource) ? $resource : 'type '.gettype($resource));

        throw new \RuntimeException(
            sprintf('No acceptable loader found for resrouce %s', $error)
        );
    }
}
