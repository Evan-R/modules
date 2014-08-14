<?php

/**
 * This File is part of the Selene\Module\Config\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Traits;

use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Config\Loader\BuilderAwareLoaderListener;

/**
 * @class ContainerBuilderAwareLoaderTrait
 * @package Selene\Module\Config\Traits
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
