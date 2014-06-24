<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI;

/**
 * @interface BuilderInterface
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface BuilderInterface
{
    public function build();

    public function merge(BuilderInterface $builder);

    public function addFileResource($file);

    public function addObjectResource($object);

    public function addExtensionConfig($extension, array $config);

    public function replaceContainer(ContainerInterface $container);

    public function getContainer();

    public function getProcessor();

    public function getExtensionConfig($extension);

    public function getExtensionConfigs();
}
