<?php

/**
 * This File is part of the Selene\Module\Writer\Tests\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\Object;

use \Selene\Module\Writer\Object\DocBlock;

/**
 * @class DocBlockTest
 * @package Selene\Module\Writer\Tests\Object
 * @version $Id$
 */
class DocBlockTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGenerateBlockComments()
    {
        $this->assertSame("/**\n */", (new DocBlock)->generate());
    }

    /** @test */
    public function itShouldAddDescription()
    {
        $doc = new DocBlock;
        $doc->setDescription('test');

        $this->assertSame("/**\n * test\n */", $doc->generate());
    }

    /** @test */
    public function itShouldAddLongDescription()
    {
        $expected = <<<EOL
/**
 * test
 *
 * A
 * B
 */
EOL;
        $doc = new DocBlock;
        $doc->setDescription('test');
        $doc->setLongDescription("A\nB");

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldAddAnnotations()
    {
        $expected = <<<EOL
/**
 * @name foo
 * @param string \$bar
 */
EOL;
        $doc = new DocBlock;
        $doc->addAnnotation('name', 'foo');
        $doc->addParam('string', 'bar');

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteFullBlock()
    {

        $expected = <<<EOL
/**
 * Foo
 *
 * Bar
 * Baz
 *
 * @name foo
 * @param string \$bar
 *
 * @return string|null description
 */
EOL;
        $doc = new DocBlock;
        $doc->setDescription('Foo');
        $doc->setLongDescription("Bar\nBaz");
        $doc->addAnnotation('name', 'foo');
        $doc->addParam('string', 'bar');
        $doc->setReturn('string|null', 'description');

        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteDescAndReturn()
    {

        $expected = <<<EOL
/**
 * Foo
 *
 * @return void
 */
EOL;
        $doc = new DocBlock;
        $doc->setDescription('Foo');
        $doc->setReturn('void');
        $this->assertSame($expected, $doc->generate());
    }

    /** @test */
    public function itShouldWriteNewLinesOnAnnotations()
    {

        $expected = <<<EOL
/**
 * @name foo
 *
 * @name bar
 */
EOL;
        $doc = new DocBlock;
        $doc->setAnnotations([
            ['name', 'foo'],
            null,
            ['name', 'bar']
        ]);
        $this->assertSame($expected, $doc->generate());
    }
}
