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

use \Selene\Components\Routing\Matchers\StaticPathMatcher;

/**
 * @class StaticPathMatcherTest
 * @package Selene\Components\Routing\Tests\Matchers
 * @version $Id$
 */
class StaticPathMatcherTest extends MatcherTest
{
    /** @test */
    public function itShouldMatchStaticPaths()
    {
        $route = $this->getRoute('/foo/{any}');

        $matcher = new StaticPathMatcher;

        $this->assertTrue($matcher->matches($route, '/foo'));
        $this->assertFalse($matcher->matches($route, '/bar'));
    }
}
