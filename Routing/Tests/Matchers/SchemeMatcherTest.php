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
use \Selene\Module\Routing\Matchers\SchemeMatcher;

/**
 * @class SchemeMatcherTest
 * @package Selene\Module\Routing\Tests\Matchers
 * @version $Id$
 */
class SchemeMatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldMatchSchemes()
    {
        $route = new Route('foo', '/');

        $route->setAction('action');

        $route->setSchemes(['https']);
        $route->compile();

        $matcher = new SchemeMatcher;

        $this->assertFalse($matcher->matches($route, 'http'));

        $this->assertTrue($matcher->matches($route, 'https'));

        $route = new Route('foo', '/');

        $route->setAction('action');

        $route->setSchemes(['https', 'http']);
        $route->compile();

        $this->assertTrue($matcher->matches($route, 'http'));
        $this->assertTrue($matcher->matches($route, 'https'));
    }
}
