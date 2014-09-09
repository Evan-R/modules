<?php

/*
 * This File is part of the Selene\Module\Routing\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Filter;

use \Selene\Module\Routing\Event\RouteFilter;
use \Selene\Module\Routing\Event\RouterEvents;

/**
 * @class AbstractFilter
 *
 * @package Selene\Module\Routing\Filter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
abstract class AbstractFilter implements FilterInterface
{
    private $name;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setFilterName($name);
    }

    public function run(RouteFilter $event)
    {
    }

    final public function getName()
    {
        return $this->name;
    }

    private function setFilterName($name)
    {
        $this->name = $name;
        //$type = $this->getType();

        //if (null === ($base = self::T_BEFORE & $type ? RouterEvents::FILTER_BEFORE :
            //(self::T_AFTER & $type ? RouterEvents::FILTER_AFTER : null))) {
            //throw new \InvalidArgumentException('Invalid filter name.');
        //}

        //$this->name = $base . '.' . $name;
    }
}
