<?php

/**
 * This File is part of the Selene\Components\Config\Resource package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

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
abstract class AbstractLoader implements LoaderInterface
{
    /**
     * observers
     *
     * @var \SplObjectStorage
     */
    private $listeners;

    /**
     * resolver
     *
     * @var LoaderResolverInterface
     */
    protected $resolver;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $any = self::LOAD_ONE)
    {
        foreach ((array)$this->findResource($resource, $any) as $file) {
            $this->loadResource($file);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * {@inheritdoc}
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
     * addListener
     *
     * @param LoaderListener $listener
     *
     * @return void
     */
    public function addListener(LoaderListener $listener)
    {
        $this->getListeners()->attach($listener);
    }

    /**
     * removeListener
     *
     * @param LoaderListener $listener
     *
     * @return void
     */
    public function removeListener(LoaderListener $listener)
    {
        $this->getListeners()->detach($listener);
    }

    /**
     * getObservers
     *
     * @return \SplObjectStorage
     */
    private function getListeners()
    {
        if (null === $this->listeners) {
            $this->listeners = new \SplObjectStorage;
        }

        return $this->listeners;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function supports($resource);

    /**
     * Actually loads the resource.
     *
     * @param mixed $resource
     *
     * @return void
     */
    abstract protected function doLoad($resource);

    /**
     * Get the file path to the resource.
     *
     * @param mixed $resource
     *
     * @return string
     */
    abstract protected function findResource($resource, $any = self::LOAD_ONE);

    /**
     * notify
     *
     * @return void
     */
    protected function notify($resource)
    {
        foreach ($this->getListeners() as $listener) {
            $listener->onLoaded($resource);
        }
    }

    /**
     * loadResource
     *
     * @param mixed $resource
     *
     * @return void
     */
    private function loadResource($resource)
    {
        $this->doLoad($resource);
        $this->notify($resource);
    }
}
