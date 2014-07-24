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

use \Selene\Components\Config\Loader\LoaderListener;

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
    /**
     * Builds the service container.
     *
     * @return vpod
     */
    public function build();

    /**
     * Merge two builder instances.
     *
     * @param BuilderInterface $builder
     *
     * @return void
     */
    public function merge(BuilderInterface $builder);

    /**
     * Adds a file resource for resource tracking.
     *
     * @param string $file
     *
     * @return void
     */
    public function addFileResource($file);

    /**
     * Adds a object resource for resource tracking.
     *
     * @param object $object
     *
     * @return void
     */
    public function addObjectResource($object);

    /**
     * Replace the current container.
     *
     * @param ContainerInterface $container
     *
     * @access void
     */
    public function replaceContainer(ContainerInterface $container);

    /**
     * Get the current container.
     *
     * @return \Selene\Components\DI\ContainerInterface
     */
    public function getContainer();

    /**
     * Get the processor.
     *
     * @return ProcessorInterface
     */
    public function getProcessor();

    /**
     * addPackageConfig
     *
     * @param string $extension
     * @param array $config
     * @access public
     *
     * @return void
     */
    public function addPackageConfig($extension, array $config);

    /**
     * getPackageConfig
     *
     * @param string $extension
     *
     * @return array
     */
    public function getPackageConfig($extension);

    /**
     * getPackageConfigs
     *
     * @return array
     */
    public function getPackageConfigs();
}
