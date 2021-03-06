<?php

/**
 * This File is part of the Selene\Module\Config\Tests\Validator\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator\Nodes;

use \Selene\Module\Config\Validator\Builder;

/**
 * @class Macro
 * @package Selene\Module\Config\Tests\Validator\Nodes
 * @version $Id$
 */
class Macro
{
    protected $name;

    protected $builder;

    public function __construct($name)
    {
        $this->name = $name;
        $this->builder = new Builder;
    }

    public function start()
    {

    }

    public function stop()
    {

    }

    public function getName()
    {
        return $this->name;
    }
}
