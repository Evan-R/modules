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

    abstract public function load($resource);

    abstract public function supports($resource);

    abstract public function import($resrouce);
}
