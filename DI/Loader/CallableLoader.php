<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Loader;

use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Config\Traits\ContainerBuilderAwareLoaderTrait;
use \Selene\Module\Config\Loader\LoaderAwareListener;
use \Selene\Module\Config\Loader\CallableLoader as BaseCallableLoader;

/**
 * @class CallableLoader extends Loader
 * @see ConfigLoader
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
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
    public function __construct(BuilderInterface $builder)
    {
        $this->setBuilder($builder);
    }

    /**
     * {@inheritdoc}
     */
    protected function doLoad($callable)
    {
        call_user_func($callable, $this->builder);
    }
}
