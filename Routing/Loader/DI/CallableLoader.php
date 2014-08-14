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
use \Selene\Module\Routing\Loader\CallableLoader as BaseCallableLoader;

/**
 * @class CallableLoader
 * @package Selene\Module\Routing\DI\Loader
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
