<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config;

use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\Config\Validator\Builder;
use \Selene\Module\Config\Validator\Validator;
use \Selene\Module\Config\Validator\Nodes\RootNode;
use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Config\Resource\LocatorInterface;

/**
 * @class Configuration
 * @package Selene\Module\Config
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
     * @return array
     */
    final public function validate(array $config)
    {
        $builder = $this->newValidatorBuilder();

        $this->configure($root = $builder->getRoot());

        $validator = $builder->getValidator();
        $validator->load($config);

        return $validator->validate();
    }

    /**
     * getConfigTree
     *
     * @return void;
     */
    abstract protected function configure(RootNode $rootNode);

    /**
     * getConfigBuilder
     *
     * @access public
     * @return Builder
     */
    public function getConfigBuilder($name = 'root')
    {
        if (null === $this->builder) {
            $this->builder = $this->newValidatorBuilder($name);
        }

        return $this->builder;
    }

    /**
     * newValidatorBuilder
     *
     * @param string $name
     *
     * @return Builder
     */
    protected function newValidatorBuilder($name = null)
    {
        return new Builder($name);
    }
}
