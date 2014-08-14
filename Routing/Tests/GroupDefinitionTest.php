<?php

/**
 * This File is part of the Selene\Module\Routing\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Tests;

use \Selene\Module\Routing\GroupDefinition;

/**
 * @class GroupDefinitionTest
 * @package Selene\Module\Routing\Tests
 * @version $Id$
 */
class GroupDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldBeInstantiable()
    {
        $this->assertInstanceof('Selene\Module\Routing\GroupDefinition', new GroupDefinition('foo', []));
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
