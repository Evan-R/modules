<?php

/**
 * This File is part of the Selene\Components\DependencyInjection\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\DependencyInjection\Container;
use Selene\Components\DependencyInjection\Definition;
use Selene\Components\DependencyInjection\ContainerInterface;

/**
 * @class ContainerTest extends TestCase ContainerTest
 * @see TestCase
 *
 * @package Selene\Components\DependencyInjection\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class DefinitionTest extends TestCase
{
    /**
     * @test
     */
    public function testDefinitionSetScope()
    {
        $definition = new Definition('StdClass');
        $this->assertSame(ContainerInterface::SCOPE_CONTAINER, $definition->getScope());

        $definition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        $this->assertSame(ContainerInterface::SCOPE_PROTOTYPE, $definition->getScope());
    }

    /**
     * @test
     */
    public function testDefinitionScopeIsContainer()
    {
        $definition = new Definition('StdClass');
        $this->assertTrue($definition->scopeIsContainer());
    }

    /**
     * @test
     */
    public function testDefinitionAddScope()
    {
        $scope = 'controller';

        $definition = new Definition('StdClass');
        $definition->addScope($scope);

        $this->assertSame('controller', $scope & $definition->getScope());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testDefinitionAddScopeShouldThrowException()
    {
        $definition = new Definition('StdClass');
        $definition->addScope(ContainerInterface::SCOPE_PROTOTYPE);
    }

    /**
     * @test
     */
    public function testDefinitionGetResolvedScope()
    {
        $definition = new Definition('StdClass');
        $class = new \StdClass;

        $this->assertFalse($definition->isResolved());

        $definition->setResolved($class);

        $this->assertTrue($definition->isResolved());
        $this->assertSame($class, $definition->getResolved());
    }

    /**
     * @test
     */
    public function testDefinitionIsResolvedOnPrototypeScope()
    {
        $definition = new Definition('StdClass');
        $definition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        $class = new \StdClass;

        $definition->setResolved($class);
        $this->assertFalse($definition->isResolved());
        $this->assertNull($definition->getResolved());
    }
}
