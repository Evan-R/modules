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
        $builder = new Builder;

        $root = $builder->root()
            ->macro('test', function ($node) {
                $node
                    ->boolean('required')
                    ->end();
            })
            ->dict('node_a')->useMacro('test')
            ->end()
            ->dict('node_b')->useMacro('test')
            ->end()->getRoot();

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
    public function itShouldGetValidatorAndValidate()
    {
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
                ->condition()
                    ->ifEmpty()
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
