<?php

/**
 * This File is part of the Selene\Components\Config\Tests\Validator package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Tests\Validator;

use \Selene\Components\Config\Validator\Builder;
use \Selene\Components\Config\Validator\Exception\MissingValueException;

/**
 * @class BuilderTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\Config\Tests\Validator
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class BuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShoudBeInstantiable()
    {
        $builder = new Builder;
        $this->assertInstanceof('Selene\Components\Config\Validator\Builder', $builder);
    }

    /** @test */
    public function itShouldCreateIntegerNode()
    {
        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\IntegerNode',
            $builder->root()->integer('int')
        );
    }

    /** @test */
    public function itShouldCreateFloatNode()
    {
        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\FloatNode',
            $builder->root()->float('double')
        );
    }

    /** @test */
    public function itShouldCreateStringNode()
    {
        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\StringNode',
            $builder->root()->string('string')
        );

    }

    /** @test */
    public function itShouldCreateBooleanNode()
    {
        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\BooleanNode',
            $builder->root()->boolean('bool')
        );
    }

    /** @test */
    public function itShouldCreateListNode()
    {
        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\ListNode',
            $builder->root()->values('array')
        );
    }

    /** @test */
    public function itShouldCreateDictNode()
    {
        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\DictNode',
            $builder->root()->dict('array')
        );
    }

    /** @test */
    public function endShouldReturnBuilder()
    {

        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Builder',
            $builder->root()->dict('array')->end(),
            'End node should be the builder.'
        );

        $builder = new Builder;
        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Builder',
            $builder->root()->dict('array')->dict('nested')->end(),
            'End node should be the builder.'
        );
    }

    /** @test */
    public function itShouldThrowIfEndExceedsRoottNode()
    {
        $builder = new Builder;
        try {
            $builder->root()->dict('array')->dict('nested')->end()->end()->end();
        } catch (\BadMethodCallException $e) {
            $this->assertSame(get_class($builder).'::end(): Node root is already root.', $e->getMessage());

            return;
        }

        $this->faile('Test slipped.');
    }

    /** @test */
    public function itShouldAddNnodes()
    {
        $builder = new Builder;
        $b = $builder->root()
            ->boolean('falsy or truthy')
                ->optional()
                ->defaultValue(true)
                ->end();
        $this->assertInstanceof('Selene\Components\Config\Validator\Builder', $b);
    }

    /** @test */
    public function itShouldHandleMacros()
    {
        $builder = new Builder('tree');

        $root = $builder->root()
            ->macro('test', function ($node) {
              $node
                  ->boolean('required')
                  ->end();
            })
            ->dict('node_a')->useMacro('test')->end()
            ->dict('node_b')->useMacro('test')->end()
            ->getRoot();

        $root->finalize(['node_a' => ['required' => false], 'node_b' => ['required' => true]]);

        $root->validate();

        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\BooleanNode',
            $a = $root->getFirstChild()->findChildByKey('required')
        );

        $this->assertInstanceof(
            'Selene\Components\Config\Validator\Nodes\BooleanNode',
            $b = $root->getLastChild()->findChildByKey('required')
        );

        $this->assertTrue($a !== $b);
    }

    /** @test */
    public function macrosShouldBePortable()
    {
        $builder = new Builder('tree');

        $callback = function () {
            $builder = new Builder('subtree');
            $builder->macro('test_b', function ($node) {
                $node->string('test_b_string')->end();
            });

            $builder->getRoot()
                ->useMacro('test_a')
                ->useMacro('test_b');

            return $builder->getRoot();
        };

        $builder->macro('test_a', function ($node) {
            $node->string('test_a_string')->end();
        })
            ->append(call_user_func($callback));

        $this->assertTrue((bool)$builder->getMacro('test_a'));
        $this->assertTrue((bool)$builder->getMacro('test_b'));

        $builder->getRoot()->finalize(['subtree' => ['test_a_string' => 'a']]);

        try {
            $builder->getRoot()->validate();
        } catch (MissingValueException $e) {
            $this->assertSame('tree[subtree][test_b_string] is required but missing.', $e->getMessage());

            return;
        }

        $this->fail('tree[subtree][test_b_string] should be missing.');
    }

    /** @test */
    public function itShouldThrowExceptionIfMacroIsNotFound()
    {
        $builder = new Builder;

        try {
            $builder->getMacro('foo');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Macro foo doesn\'t exist.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldThrowExceptionIfNodeHasNoKey()
    {
        $builder = new Builder;

        try {
            $builder->getRoot()
                ->dict()
                ->end();
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Key can\'t be null.', $e->getMessage());
        }
    }

    /** @test */
    public function itShouldntApplyMacrosOnScalars()
    {

        $builder = new Builder;

        try {
            $builder->root()
                ->macro('test', function ($node) {
                    $node
                        ->boolean('required')
                        ->end();
                })
                ->string('node_a')->useMacro('test')
                ->end();
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Can’\t use a macro on a scalar node', $e->getMessage());

            return;
        }

        $this->fail('test slipped.');
    }

    /** @test */
    public function requiredNodeMustNotBeEmpty()
    {
        $builder = new Builder;
        $builder->root()
            ->string('test')->end();

        $validator = $builder->getValidator();
        $validator->load([]);

        try {
            $validator->validate();
        } catch (MissingValueException $e) {
            $this->assertEquals('root[test] is required but missing.', $e->getMessage());
        }

        $builder = new Builder;
        $builder->root()
            ->dict('test')
                ->string('foo')->end()
                ->string('bar')->end()
            ->end();

        $validator = $builder->getValidator();
        $validator->load(['test' => ['foo' => 'bar']]);

        try {
            $validator->validate();
        } catch (MissingValueException $e) {
            $this->assertEquals('root[test][bar] is required but missing.', $e->getMessage());

            return;
        }

        $this->fail();
    }

    /** @test */
    public function itShouldReplaceMissingValuesWhenOptional()
    {
        $builder = new Builder;
        $builder->root()
            ->string('test')
                ->defaultValue('test')
                ->optional()
                ->end();

        $validator = $builder->getValidator();
        $validator->load([]);
        $validator->validate();

        $this->assertSame(['test' => 'test'], $validator->validate());
    }

    /** @test */
    public function itShouldGetValidatorAndValidate()
    {
        //$this->markTestIncomplete();
        $builder = new Builder;

        $builder->root()
            ->macro('test', function ($node) {
                $node
                    ->boolean('foo')
                    ->end()
                    ->string('bar')
                    ->end();
            })
            ->dict('bar')
                ->useMacro('test')
            ->end()
            ->dict('testme')
                ->optional()
                ->condition()
                    ->ifMissing()
                    ->then(function () {
                        return ['inserted' => true, 'test' => true];
                    })
                ->end()
                ->boolean('test')->end()
                ->end()
                ->append($this->getParamsSection())
                ->end()
                ->values('list')->notEmpty()
                    ->dict()
                        ->string('foo')
                            ->condition()
                            ->ifEmpty()
                            ->then(function () {
                                return 'replacement';
                            })->end()
                        ->notEmpty()->end()
                    ->end()
                ->end();

        $validator = $builder->getValidator();

        $validator->load(['foo' => 'string', 'bar' => ['foo' => false, 'bar' => 'some string'],
            'parameters' => [
                'required' => false
            ],
            'list' => [
                ['foo' => 'dasd'],
                ['foo' => 'asd'],
                ['foo' => ''],
            ]
        ]);

        $this->assertTrue(is_array($res = $validator->validate()));

        $this->assertTrue(isset($res['list']) && 'replacement' === $res['list'][2]['foo']);
    }

    protected function getParamsSection()
    {
        $builder = new Builder;
        $builder
            ->setRoot('parameters')
                ->boolean('required')
            ->end();

        return $builder->getRoot();
    }
}
