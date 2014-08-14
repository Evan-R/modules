<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Definition package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Definition;

use \Mockery as m;
use \Selene\Module\DI\ContainerInterface;
use \Selene\Module\DI\Definition\ClassDefinition;

/**
 * @class ClassDefinitionTest
 * @package Selene\Module\DI\Tests\Definition
 * @version $Id$
 */
class ClassDefinitionTest extends DefinitionTest
{
    protected function setUp()
    {
        $this->defaultScope = ContainerInterface::SCOPE_PROTOTYPE;
    }

    /**
     * @test
     */
    public function tesSetInjectOnPrototypeDefinition()
    {
        m::mock($className = 'Foo\\Bar\\ClassDefinitionTestClassMock');
        $def = new ClassDefinition($className);

        try {
            $def->setInjected(true);
        } catch (\LogicException $e) {
            $this->assertEquals($e->getMessage(), 'Cannot inject a class that has not container scope.');
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    /**
     * @test
     */
    public function testChangeScopeOnjectedDefinition()
    {
        m::mock($className = 'Foo\\Bar\\ClassDefinitionTestClassMock');

        $def = new ClassDefinition($className, [], ContainerInterface::SCOPE_CONTAINER);
        $def->setInjected(true);

        try {
            $def->setScope(ContainerInterface::SCOPE_PROTOTYPE);
        } catch (\LogicException $e) {
            $this->assertEquals($e->getMessage(), 'Cannot set prototype scope on an injected class.');
        } catch (\Exception $e) {
            $this->fail();
        }
    }

    protected function tearDown()
    {
        m::close();
        parent::tearDown();
    }

    protected function createDefinition($class = null, $arguments = [], $scope = null)
    {
        return new ClassDefinition($class, $arguments, $scope);
    }
}
