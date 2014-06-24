<?php

/**
 * This File is part of the Selene\Components\Filesystem\Tests\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem\Tests\Traits;

use \Selene\Components\Filesystem\Traits\PathHelperTrait;

/**
 * @class PathHelperTraitTest
 * @package Selene\Components\Filesystem\Tests\Traits
 * @version $Id$
 */
class PathHelperTraitTest extends \PHPUnit_Framework_TestCase
{
    use PathHelperTrait;

    /** @test */
    public function itShouldReckognizeRelativePaths()
    {
        $this->assertTrue($this->isRelativePath('foo/bar'));

        $this->assertFalse($this->isRelativePath('/foo/bar'));
        $this->assertFalse($this->isRelativePath('C://foo/bar'));
        $this->assertFalse($this->isRelativePath('file:///foo/bar'));
    }

    /** @test */
    public function itShouldReckognizeAbsolutePaths()
    {
        $this->assertFalse($this->isAbsolutePath('foo/bar'));

        $this->assertTrue($this->isAbsolutePath('/foo/bar'));
        $this->assertTrue($this->isAbsolutePath('C://foo/bar'));
        $this->assertTrue($this->isAbsolutePath('file:///foo/bar'));
    }

    /** @test */
    public function itShouldExpandPaths()
    {
        $this->assertSame('foo/bar', $this->expandPath('foo/bar/baz/../'));
        $this->assertSame('foo/bar/bam', $this->expandPath('foo/bar/baz/../bam'));
    }

    /** @test */
    public function testShouldSubstitutePaths()
    {
        $this->assertSame('target', $this->substitutePaths('/foo/bar/baz', '/foo/bar/baz/target'));

        try {
            $this->substitutePaths('/foo/bar/baz', '/foo/bam/baz/target');
        } catch (\InvalidArgumentException $e) {
            $this->assertSame('Root path does not contain current path', $e->getMessage());
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test failed');
    }
}
