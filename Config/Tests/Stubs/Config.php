<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Tests\Stubs;

use \Selene\Module\DI\BuilderInterface;
use \Selene\Module\Config\Configuration;
use \Selene\Module\Config\Validator\Nodes\RootNode;
use \Selene\Module\Config\Resource\LocatorInterface;

/**
 * @class Config
 * @package Selene\Module\Config\Tests\Stubs
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
