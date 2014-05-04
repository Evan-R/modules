<?php

/**
 * This File is part of the Selene\Components\DI\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests;

use Selene\Components\TestSuite\TestCase;
use Selene\Components\DI\Container;
use Selene\Components\DI\Definition\ServiceDefinition;
use Selene\Components\DI\ContainerInterface;

/**
 * @class ContainerTest extends TestCase ContainerTest
 * @see TestCase
 *
 * @package Selene\Components\DI\Tests
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
        $definition = new ServiceDefinition('StdClass');
        $this->assertSame(ContainerInterface::SCOPE_CONTAINER, $definition->getScope());

        $definition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        $this->assertSame(ContainerInterface::SCOPE_PROTOTYPE, $definition->getScope());
    }

    /**
     * @test
     */
    public function testDefinitionScopeIsContainer()
    {
        $definition = new ServiceDefinition('StdClass');
        $this->assertTrue($definition->scopeIsContainer());
    }

    /**
     * @test
     */
    public function testDefinitionAddScope()
    {
        $scope = 'controller';

        $definition = new ServiceDefinition('StdClass');
        $definition->addScope($scope);

        $this->assertSame('controller', $scope & $definition->getScope());
    }

    /**
     * @test
     * @expectedException InvalidArgumentException
     */
    public function testDefinitionAddScopeShouldThrowException()
    {
        $definition = new ServiceDefinition('StdClass');
        $definition->addScope(ContainerInterface::SCOPE_PROTOTYPE);
    }
}
