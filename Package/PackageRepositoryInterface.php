<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\BuilderInterface as ContainerBuilderInterface;

/**
 * @interface PackageRepositoryInterface
 *
 * @package Selene\Components\Package
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface PackageRepositoryInterface
{
    public function add(PackageInterface $package);

    public function addPackages(array $packages);

    public function get($name);

    public function has($name);

    public function all();

    public function build(ContainerBuilderInterface $builder);

    public function boot();

    public function shutDown();
}
