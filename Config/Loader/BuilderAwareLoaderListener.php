<?php

/**
 * This File is part of the Selene\Module\Config\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Loader;

use \Selene\Module\DI\BuilderInterface;

/**
 * @class BuilderAwareLoaderListener
 * @package Selene\Module\Config\Loader
 * @version $Id$
 */
class BuilderAwareLoaderListener implements LoaderListener
{
    /**
     * builder
     *
     * @var BuilderInterface
     */
    private $builder;

    /**
     * Constructor.
     *
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function onLoaded($resource)
    {
        if (is_object($resource)) {
            $this->builder->addObjectResource($resource);
        } elseif (is_string($resource) && is_file($resource)) {
            $this->builder->addFileResource($resource);
        }
    }
}
