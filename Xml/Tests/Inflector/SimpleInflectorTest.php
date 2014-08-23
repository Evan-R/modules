<?php

/*
 * This File is part of the Selene\Module\Xml\Tests\Inflector package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Xml\Tests\Inflector;

use \Selene\Module\TestSuite\TestCase;
use \Selene\Module\Xml\Inflector\SimpleInflector;

/**
 * @class SimpleInflectorTest
 *
 * @package Selene\Module\Xml\Tests\Inflector
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class SimpleInflectorTest extends TestCase
{
    /**
     * @test
     * @dataProvider singularProvider
     */
    public function itShouldPluralizeStrings($singular, $plural)
    {
        $inflector = new SimpleInflector(true);

        $this->assertEquals($plural, $inflector->pluralize($singular));
    }

    /**
     * @test
     * @dataProvider singularProvider
     */
    public function itShouldSingularizeStrings($singular, $plural)
    {
        $inflector = new SimpleInflector(true);

        $this->assertEquals($singular, $inflector->singularize($plural));
    }

    /** @test */
    public function itShouldNotSingularizeStringOnNoMatchAndNoTrailing()
    {
        $inflector = new SimpleInflector(true);
        $this->assertEquals('xx', $inflector->singularize('xx'));

        $inflector = new SimpleInflector(false);
        $this->assertEquals('xx', $inflector->singularize('xx'));
    }

    /** @test */
    public function itShouldPluralizeStringOnNoMatch()
    {
        $inflector = new SimpleInflector(true);
        $this->assertEquals('asxs', $inflector->pluralize('asx'));

        $inflector = new SimpleInflector(false);
        $this->assertEquals('ax', $inflector->pluralize('ax'));
    }

    public function singularProvider()
    {
        return [
            ['fox', 'foxes'],
            ['box', 'boxes'],
            ['entity', 'entities'],
            ['repository', 'repositories'],
            ['alias', 'aliases'],
            ['pun', 'puns'],
        ];
    }
}
