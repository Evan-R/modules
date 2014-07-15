<?php

/**
 * This File is part of the Selene\Components\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Tests;

use \Selene\Components\Routing\GroupDefinition;

/**
 * @class GroupDefinitionTest
 * @package Selene\Components\Routing\Tests
 * @version $Id$
 */
class GroupDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Components\Routing\GroupDefinition', new GroupDefinition('foo', []));
    }

    /** @test */
    public function itShouldHaveAPrefix()
    {
        $groupA = new GroupDefinition('foo/', []);

        $this->assertSame('/foo', $groupA->getPrefix());

        $groupB = new GroupDefinition('bar/', [], $groupA);

        $this->assertSame('/foo/bar', $groupB->getPrefix());
    }

    /** @test */
    public function itShouldMergeRequirements()
    {
        $groupA = new GroupDefinition('foo/', ['before' => 'auth']);

        $groupB = new GroupDefinition('bar/', ['after' => 'filter'], $groupA);

        $this->assertSame(['_before' => 'auth', '_after' => 'filter'], $groupB->getRequirements());
    }
}
