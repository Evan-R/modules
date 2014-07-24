<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Loader;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Config\Traits\ContainerBuilderAwareLoaderTrait;
use \Selene\Components\Config\Loader\LoaderAwareListener;
use \Selene\Components\Config\Loader\CallableLoader as BaseCallableLoader;

/**
 * @class CallableLoader extends Loader
 * @see ConfigLoader
 *
 * @package Selene\Components\DI
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
    protected function doLoad($file)
    {
        call_user_func($callable, $this->builder);
    }
}
