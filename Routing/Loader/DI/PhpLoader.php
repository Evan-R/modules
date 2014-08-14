<?php

/**
 * This File is part of the Selene\Module\Routing\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Loader\DI;

use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Routing\RouteCollectionInterface;
use \Selene\Module\Config\Traits\ContainerBuilderAwareLoaderTrait;
use \Selene\Module\Config\Resource\LocatorInterface;
use \Selene\Module\Routing\Loader\PhpLoader as BasePhpLoader;

/**
 * @class PhpLoader
 * @package Selene\Module\Routing\DI\Loader
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
