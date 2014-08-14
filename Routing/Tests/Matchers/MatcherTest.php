<?php

/**
 * This File is part of the Selene\Module\Routing\Tests\Matchers package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests\Matchers;

use \Selene\Module\Routing\Route;

/**
 * @class MatcherTest
 * @package Selene\Module\Routing\Tests\Matchers
 * @version $Id$
 */
abstract class MatcherTest extends \PHPUnit_Framework_TestCase
{
    protected function getRoute($path, array $constraits = [], $methods = ['GET'], $host = null)
    {
        $route = new Route('any', $path, $methods);
        $route->setAction('action');

        if (isset($constraits['path'])) {
            foreach ($constraits['path'] as $var => $constraint) {
                $route->setConstraint($var, $constraint);
            }
        }

        if (isset($constraits['host'])) {
            foreach ($constraits['host'] as $var => $constraint) {
                $route->setHostConstraint($var, $constraint);
            }
        }

        if (null !== $host) {
            $route->setHost($host);
        }

        $route->compile();

        return $route;
    }
}
