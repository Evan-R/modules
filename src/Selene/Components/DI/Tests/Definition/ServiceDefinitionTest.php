<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Definition;

use \Mockery as m;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Definition\ServiceDefinition;

/**
 * @class ServiceDefinitionTest
 * @package Selene\Components\DI
 * @version $Id$
 */
class ServiceDefinitionTest extends DefinitionTest
{
    protected function setUp()
    {
        $this->defaultScope = ContainerInterface::SCOPE_CONTAINER;
    }

    /** @test */
    public function testDefinitionScopeIsContainer()
    {
        $definition = new ServiceDefinition('stdClass');
        $this->assertTrue($definition->scopeIsContainer());
    }

    /** @test */
    public function testDefinitionSetScope()
    {
        $definition = $this->createDefinition('stdClass', [], ContainerInterface::SCOPE_CONTAINER);
        $this->assertSame(ContainerInterface::SCOPE_CONTAINER, $definition->getScope());

        $definition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        $this->assertSame(ContainerInterface::SCOPE_PROTOTYPE, $definition->getScope());
    }

    /** @test */
    public function testDefinitionAddScope()
    {
        $scope = ContainerInterface::SCOPE_CONTAINER;

        $definition = new ServiceDefinition('StdClass');
        $definition->addScope($scope);

        $this->assertSame(ContainerInterface::SCOPE_CONTAINER, $scope & $definition->getScope());
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

    protected function createDefinition($class = null, $arguments = [], $scope = null)
    {
        return new ServiceDefinition($class, $arguments, $scope);
    }
}