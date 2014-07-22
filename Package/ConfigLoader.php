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

use \Selene\Components\DI\Parameters;
use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Config\ConfigurationInterface;

/**
 * @class ConfigLoader
 * @package Selene\Components\Package
 * @version $Id$
 */
class ConfigLoader
{
    /**
     * loaded
     *
     * @var array
     */
    private $loaded;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->loaded = [];
    }

    /**
     * load
     *
     * @param BuilderInterface $builder
     * @param PackageInterface $package
     *
     * @return void
     */
    public function load(BuilderInterface $builder, PackageInterface $package)
    {

        if (!($config = $package->getConfiguration()) instanceof ConfigurationInterface) {
            return;
        }

        $container = $builder->getContainer();

        $pContainer = $this->getContainer($container, $this->getRequirements($package));

        //$parameters = $container->getParameters();
        //$containerClass = (new \ReflectionObject($container))->getName();
        //$packageContainer = new $container;
        //$packageContainer->getParameters()->merge(new Parameters($parameters->getRaw()));

        $builder->replaceContainer($pContainer);

        $config->load($builder, $builder->getPackageConfig($alias = $package->getAlias()));

        $container->merge($pContainer);

        $builder->replaceContainer($container);
        $builder->addObjectResource($config);
        $builder->addObjectResource($package);

        $this->loaded[$alias] = $pContainer;
    }

    /**
     * unload
     *
     * @return void
     */
    public function unload()
    {
        unset($this->loaded);

        $this->loaded = [];
    }

    /**
     * getRequirements
     *
     * @return array
     */
    protected function getRequirements(PackageInterface $package)
    {
        $requirements = [];

        foreach ((array)$package->requires() as $requirement) {
            $req = rtrim($requirement, '?');

            if (isset($this->loaded[$req])) {
                $requirements[$req] =& $this->loaded[$req];
            }
        }

        return $requirements;
    }

    /**
     * getContainer
     *
     * @param ContainerInterface $container
     * @param array $requirements
     *
     * @return ContainerInterface
     */
    protected function getContainer(ContainerInterface $container, array $requirements)
    {
        $class = (new \ReflectionObject($container))->getName();

        if (empty($requirements)) {
            return new $class(new Parameters($container->getParameters()->all()));
        }

        $con = $container;

        foreach ($requirements as $alias => $requirement) {
            $parameters = $con->getParameters();
            $pContainer = new $class(new Parameters($parameters->all()));
            $pContainer->merge($con);
            $con = $pContainer;
        }

        return $con;
    }
}
