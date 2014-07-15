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

use \Selene\Components\Routing\Matchers\HostMatcher;

/**
 * @class HostMatcherTest
 * @package Selene\Components\Routing\Tests\Matchers
 * @version $Id$
 */
class HostMatcherTest extends MatcherTest
{
    /** @test */
    public function itShouldMatchHosts()
    {
        $matcher = new HostMatcher;

        $route = $this->getRoute('/', ['host' => ['tld' => '(dev|com)']], ['GET'], 'selene.{tld}');

        $this->assertTrue($matcher->matches($route, 'selene.com'));
        $this->assertTrue($matcher->matches($route, 'selene.dev'));
        $this->assertFalse($matcher->matches($route, 'selene.de'));
    }
}
