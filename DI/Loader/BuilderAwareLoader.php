<?php

/**
 * This File is part of the Selene\Module\DI\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Loader;

use \Selene\Module\Config\Resource\Loader;

/**
 * @class BuilderAwareLoader
 * @package Selene\Module\DI\Loader
 * @version $Id$
 */
abstract class BuilderAwareLoader extends Loader
{
    /**
     * {@inheritdoc}
     */
    protected function notifyResource($resource)
    {
        $this->builder->addFileResource($resource);
    }
}
