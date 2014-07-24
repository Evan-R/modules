<?php

/**
 * This File is part of the Selene\Components\Routing\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Loader\DI;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Routing\RouteCollectionInterface;
use \Selene\Components\Config\Traits\ContainerBuilderAwareLoaderTrait;
use \Selene\Components\Config\Resource\LocatorInterface;
use \Selene\Components\Routing\Loader\PhpLoader as BasePhpLoader;

/**
 * @class PhpLoader
 * @package Selene\Components\Routing\DI\Loader
 * @version $Id$
 */
class PhpLoader extends BasePhpLoader
{
    use ContainerBuilderAwareLoaderTrait;

    public function __construct(BuilderInterface $builder, RouteCollectionInterface $routes, LocatorInterface $locator)
    {
        parent::__construct($routes, $locator);
        $this->setBuilder($builder);
    }
}
