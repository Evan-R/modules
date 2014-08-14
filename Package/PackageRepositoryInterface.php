<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package;

use \Selene\Module\DI\ContainerInterface;
use \Selene\Adapter\Kernel\ApplicationInterface;
use \Selene\Module\DI\BuilderInterface as ContainerBuilderInterface;

/**
 * @interface PackageRepositoryInterface
 *
 * @package Selene\Module\Package
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

    public function boot(ApplicationInterface $app);

    public function shutDown();
}
