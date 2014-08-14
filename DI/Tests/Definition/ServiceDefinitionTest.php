<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Definition;

use \Mockery as m;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Definition\ServiceDefinition;

/**
 * @class ServiceDefinitionTest
 * @package Selene\Module\DI
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

        $this->assertTrue($definition->hasScope(ContainerInterface::SCOPE_CONTAINER));

        $definition->addScope(25);

        $this->assertTrue($definition->hasScope(ContainerInterface::SCOPE_CONTAINER));
        $this->assertTrue($definition->hasScope(25));
    }

    /** @test */
    public function itShouldThrowLogicExceptionWhenSettingInjectedWithWrongScopeAndViceVersa()
    {
        $definition = new ServiceDefinition('StdClass');
        $definition->setScope(ContainerInterface::SCOPE_PROTOTYPE);

        try {
            $definition->setInjected(true);
        } catch (\LogicException $e) {
            $this->assertSame('Cannot inject a service that has not container scope', $e->getMessage());
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $definition = new ServiceDefinition('StdClass');
        $definition->setInjected(true);

        try {
            $definition->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        } catch (\LogicException $e) {
            $this->assertSame('Cannot set prototype scope on an injected service', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }
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
