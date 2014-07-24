<?php

/**
 * This File is part of the Selene\Components\Routing\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\DI\Loader;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Routing\RouteCollectionInterface;
use \Selene\Components\Config\Traits\ContainerBuilderAwareLoaderTrait;
use \Selene\Components\Routing\Loader\CallableLoader as BaseCallableLoader;

/**
 * @class CallableLoader
 * @package Selene\Components\Routing\DI\Loader
 * @version $Id$
 */
class CallableLoader extends BaseCallableLoader
{
    use ContainerBuilderAwareLoaderTrait;

    /**
     * Constructor.
     *
     * @param BuilderInterface $builder
     * @param LocatorInterface $locator
     */
    public function __construct(BuilderInterface $builder, RouteCollectionInterface $routes)
    {
        parent::__construct($routes);

        $this->setBuilder($builder);
    }
}
