<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

/**
 * @class DelegatingLoader extends Loader
 * @see Loader
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DelegatingLoader implements LoaderInterface
{
    /**
     * @access public
     * @return mixed
     */
    public function __construct(ResolverInterface $resolver = null)
    {
        $this->setResolver($resolver);
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
    public function load($resource, $any = false)
    {
        if ($loader = $this->getResolver()->resolve($resource)) {
            return $loader->load($resource, $any);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function import($resource)
    {
        if ($loader = $this->getResolver()->resolve($resource)) {
            return $loader->import($resolver);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource)
    {
        if (($loader = $this->getResolver()->resolve($resource)) && $loader->supports($resource)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener(LoaderListener $listener)
    {
        foreach ($this->resolver->all() as $loader) {
            $loader->addListener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener(LoaderListener $listener)
    {
        foreach ($this->resolver->all() as $loader) {
            $loader->removeListener($listener);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function findResource()
    {
    }

    protected function doLoad($file)
    {
    }

    protected function notifyResource($resource)
    {
    }
}
