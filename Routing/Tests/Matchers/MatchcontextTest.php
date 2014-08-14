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
use \Selene\Module\Routing\Matchers\MatchContext;

/**
 * @class MatchcontextTest
 * @package Selene\Module\Routing\Tests\Matchers
 * @version $Id$
 */
class MatchcontextTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itIsExpectedThat()
    {
        $route = new Route('foo', '/{any?}');

        $route->setAction('action');
        $route->setHost('selene.{tld}');
        $route->setDefault('any', 'test');
        $route->setHostDefault('tld', 'dev');

        $route->compile();

        $context = new MatchContext($route, []);

        $this->assertSame(['any' => 'test'], $context->getParameters());

        $context = new MatchContext($route, ['any' => 'bar']);

        $this->assertSame(['any' => 'bar'], $context->getParameters());

        $context = new MatchContext($route, []);

        $this->assertSame(['tld' => 'dev'], $context->getHostParameters());

        $context = new MatchContext($route, ['tld' => 'com', 'foo' => 'bar']);

        $this->assertSame(['tld' => 'com'], $context->getHostParameters());
    }
}
