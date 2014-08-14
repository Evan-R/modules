<?php

/**
 * This File is part of the Selene\Module\Writer\Tests\Generator\File package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Tests\File;

use \Selene\Module\Writer\File\JsonGenerator;

/**
 * @class JsonGeneratorTest
 * @package Selene\Module\Writer\Tests\Generator\File
 * @version $Id$
 */
class JsonGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function itShouldGenerateJsonStrings()
    {
        $js = new JsonGenerator;

        $js->addContent('foo', 'bar');
        $this->assertJsonStringEqualsJsonString(json_encode(['foo' => 'bar']), $js->generate());

        $js = new JsonGenerator;

        $js->setContent($data = [
            'foo' => [
            'bar' => [
                ]
            ]
        ]);

        $this->assertJsonStringEqualsJsonString(json_encode($data), $js->generate());

        $js = new JsonGenerator;

        $js->setContent([
            'foo' => [
            'bar' => [
                ]
            ]
        ]);

        $js->addContent('foo.bar', 'baz');
        $this->assertJsonStringEqualsJsonString(json_encode(['foo' => ['bar' => 'baz']]), $js->generate());
    }

    /** @test */
    public function itShouldFailWritingStuff()
    {

    }
}
