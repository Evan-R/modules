<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Resource;

/**
 * @class DelegatingLoader extends Loader
 * @see Loader
 *
 * @package Selene\Components\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DelegatingLoader extends Loader
{
    /**
     * @access public
     * @return mixed
     */
    public function __construct(LoaderResolverInterface $resolver = null)
    {
        $this->resolver = $resolver;
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
    public function getResourcePath()
    {
        return null;
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
    public function supports($format)
    {
        if (($loader = $this->getResolver()->resolve($resource)) && $loader->supports($format)) {
            return true;
        }

        return false;
    }

    protected function doLoad($file)
    {
    }

    protected function notifyResource($resource)
    {
    }
}
