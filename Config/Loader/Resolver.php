<?php

/**
 * This File is part of the Selene\Module\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Loader;

use \Selene\Module\Config\Exception\LoaderException;

/**
 * @class LoaderResolver
 * @package Selene\Module\Config\Resource
 * @version $Id$
 */
class Resolver implements ResolverInterface
{
    /**
     * loaders
     *
     * @var array
     */
    private $loaders;

    /**
     * Constructor.
     *
     * @param array $loaders
     */
    public function __construct(array $loaders = [])
    {
        $this->loaders = [];
        $this->setLoaders($loaders);
    }

    /**
     * resolve
     *
     * @param mixed $resource
     *
     * @throws LoaderException if no loader is found.
     * @return LoaderInterface
     */
    public function resolve($resource)
    {
        foreach ($this->loaders as $loader) {

            if ($loader->supports($resource)) {
                return $loader;
            }
        }

        throw LoaderException::missingLoader($resource);
    }

    /**
     * all
     *
     * @return array
     */
    public function all()
    {
        return $this->loaders;
    }

    /**
     * addLoader
     *
     * @param LoaderInterface $loader
     *
     * @return void
     */
    public function addLoader(LoaderInterface $loader)
    {
        $loader->setResolver($this);
        $this->loaders[] = $loader;
    }

    /**
     * setLoaders
     *
     * @param array $loaders
     *
     * @return void
     */
    public function setLoaders(array $loaders)
    {
        $this->loaders = [];

        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

}
