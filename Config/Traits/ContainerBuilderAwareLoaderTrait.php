<?php

/**
 * This File is part of the Selene\Components\Config\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Traits;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Config\Loader\BuilderAwareLoaderListener;

/**
 * @class ContainerBuilderAwareLoaderTrait
 * @package Selene\Components\Config\Traits
 * @version $Id$
 */
trait ContainerBuilderAwareLoaderTrait
{
    protected $builder;

    public function setBuilder(BuilderInterface $builder)
    {
        $this->builder = $builder;
        $this->addListener(new BuilderAwareLoaderListener($builder));
    }
}
