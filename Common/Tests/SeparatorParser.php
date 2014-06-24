<?php

/**
 * This File is part of the Selene\Components\Common\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Common\Tests;

use \Selene\Components\Common\SeparatorParser;

class NSParserTest extends \PHPUnit_Framework_TestCase
{

    /** @test */
    public function itShouldBeInstatiable()
    {
        var_dump($this->getParser());
    }

    public function itShouldNotSupportStringContainingOneOrLessIndicators()
    {
        $parser = $this->getParser();

        $this->assertFalse($parser->supports('string'));
        $this->assertFalse($parser->supports('string:string'));
        $this->assertFalse($parser->supports('string:string:'));
        $this->assertFalse($parser->supports('::string'));
        $this->assertFalse($parser->supports(':string:string'));

        $this->assertTrue($parser->supports('string:string:string'));
        $this->assertTrue($parser->supports('string::string'));
    }

    /** @test */
    public function itShouldParseParts()
    {
        $parser = $this->getParser();
        $this->assertEquals(['ns', 'cc', 'mth'], $parser->parse('ns:cc:mth'));

        $this->assertEquals(['ns', null, 'mth'], $parser->parse('ns::mth'));
    }


    protected function getParser()
    {
        return new SeparatorParser;
    }
}
