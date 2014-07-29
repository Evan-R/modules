<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Stubs;

use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\Config\Configuration;
use \Selene\Components\Config\Validator\Nodes\RootNode;
use \Selene\Components\Config\Resource\LocatorInterface;

/**
 * @class Config
 * @package Selene\Components\Config\Tests\Stubs
 * @version $Id$
 */
class Config extends Configuration
{
    private $loadedValues;

    public function load(BuilderInterface $builder, array $values)
    {
        $this->loadedValues = $values;
    }

    /**
     * getLoadedValues
     *
     * @return array
     */
    public function getLoadedValues()
    {
        return $this->loadedValues;
    }


    public function getConfigTree(RootNode $rootNode)
    {

    }
}
