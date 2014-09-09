<?php

/*
 * This File is part of the Selene\Module\Routing\Dumper package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Dumper;

use \Selene\Module\Routing\Route;
use \Selene\Module\Routing\RouteCollectionInterface;

/**
 * @class PhpDumper
 *
 * @package Selene\Module\Routing\Dumper
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PhpDumper implements DumperInteface
{
    private $routes;

    public function __construct(RouteCollectionInterface $routes)
    {
        $this->routes = $routes;
    }

    public function dump()
    {
    }
}
