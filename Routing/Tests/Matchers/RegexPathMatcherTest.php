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

use \Selene\Components\Routing\Matchers\RegexPathMatcher;

/**
 * @class RegexPathMatcherTest
 * @package Selene\Components\Routing\Tests\Matchers
 * @version $Id$
 */
class RegexPathMatcherTest extends MatcherTest
{
    /** @test */
    public function itShouldMatchWithRegexp()
    {
        $route = $this->getRoute('/{any}');
        $matcher = new RegexPathMatcher;

        $this->assertTrue($matcher->matches($route, '/foo'));

        $route = $this->getRoute('/{any}', ['path' => ['any' => '(\d+)']]);
        $matcher = new RegexPathMatcher;

        $this->assertFalse($matcher->matches($route, '/foo'));
        $this->assertTrue($matcher->matches($route, '/23'));
    }
}
