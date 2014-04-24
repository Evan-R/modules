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
        $builder->root()
            ->boolean('falsy or truthy')
                ->optional()
                ->defaultValue(true)
            ->end()
            ->boolean('Faaaaaaack')
                ->optional()
                ->defaultValue(true)
            ->end()
            ->dict('dict')
                ->dict('otherDict')
                    ->dict('yetAnotherDict')
                    ->end()
                ->end()
            ->end();

        var_dump($builder);
    }
}
