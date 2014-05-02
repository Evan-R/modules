<?php

/**
 * This File is part of the Selene\Components\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config;

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Config\Validator\Builder;
use \Selene\Components\Config\Validator\Validator;
use \Selene\Components\DI\BuilderInterface;

/**
 * @class Configuration
 * @package Selene\Components\Config
 * @version $Id$
 */
abstract class Configuration implements ConfigurationInterface
{
    /**
     * builder
     *
     * @var mixed
     */
    protected $builder;

    /**
     * loaders
     *
     * @var mixed
     */
    protected $loaders;

    /**
     * load
     *
     * @param ContainerInterface $container
     * @param array $values
     *
     * @access public
     * @abstract
     * @return mixed
     */
    abstract public function load(BuilderInterface $builder, array $values);

    /**
     * validate
     *
     * @access public
     * @return mixed
     */
    final public function validate(array $config)
    {
        $validator = new Validator($this->getConfigTree(), $config);
        return $validator->validate();
    }

    /**
     * getConfigTree
     *
     * @access public
     * @return mixed
     */
    public function getConfigTree()
    {
        return $this->getConfigBuilder()->getRoot();
    }

    /**
     * getConfigBuilder
     *
     * @access public
     * @return Builder
     */
    public function getConfigBuilder($name = 'root')
    {
        if (null === $this->builder) {
            $this->builder = new Builder($name);
        }
        return $this->builder;
    }

    /**
     * getLoaders
     *
     * @access public
     * @return mixed
     */
    public function getLoaders(LocatorInterface $locator)
    {

    }
}
