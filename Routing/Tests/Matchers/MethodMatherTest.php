<?php

/**
 * This File is part of the Selene\Components\Routing\Tests\Matchers package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests\Matchers;

use \Selene\Components\Routing\Matchers\MethodMatcher;

/**
 * @class MethodMatherTest
 * @package Selene\Components\Routing\Tests\Matchers
 * @version $Id$
 */
class MethodMatherTest extends MatcherTest
{
    /** @test */
    public function itShouldMatchHttpVerbs()
    {
        $route = $this->getRoute('/');
        $matcher = new MethodMatcher;

        $this->assertTrue($matcher->matches($route, 'GET'));
        $this->assertFalse($matcher->matches($route, 'POST'));

        $route = $this->getRoute('/', [], ['POST']);

        $this->assertFalse($matcher->matches($route, 'GET'));
        $this->assertTrue($matcher->matches($route, 'POST'));

        $route = $this->getRoute('/', [], ['POST', 'GET']);

        $this->assertTrue($matcher->matches($route, 'GET'));
        $this->assertTrue($matcher->matches($route, 'POST'));
    }
}
