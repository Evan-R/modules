<?php

/**
 * This File is part of the Selene\Components\View\Tests package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Tests;

use \Mockery as m;
use \org\bovigo\vfs\vfsStream;
use \Selene\Components\View\TemplateResolver;
use \Selene\Components\View\TemplateResolverInterface;

/**
 * @class TemplateResolverTest extends \PHPUnit_Framework_TestCase
 * @see \PHPUnit_Framework_TestCase
 *
 * @package Selene\Components\View\Tests
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class TemplateResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * templateRoot
     *
     * @var mixed
     */
    protected $templateRoot;

    /**
     * templateRootPath
     *
     * @var string
     */
    protected $templateRootPath;

    /**
     * setUp
     *
     * @access protected
     * @return mixed
     */
    protected function setUp()
    {
        $this->templateRoot = vfsStream::setUp('root/views');
        $this->templateRootPath = vfsStream::url('root/views');
    }

    /**
     * tearDown
     *
     * @access protected
     * @return mixed
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $resolver = new TemplateResolver;
        $this->assertInstanceof('Selene\Components\View\TemplateResolverInterface', $resolver);
    }

    /**
     * @test
     * @dataProvider templateProvider
     */
    public function itShouldResolveTemplatesFromTemplateRoot(array $files, $template)
    {
        $this->createFiles($files);

        $resolver = new TemplateResolver($path = $this->templateRoot());

        $resolved = $resolver->resolve($template);

        $this->assertEquals(count($files), count($resolved));

        foreach ($resolved as $i => $info) {
            $this->assertInstanceof('SplFileInfo', $info);
            $this->assertSame(basename($files[$i]), $info->getFilename());
        }
    }

    /**
     * @test
     * @dataProvider packageTemplateProvider
     */
    public function itShouldResolveTemplatesFromPackage(array $files, $template, $packages)
    {
        $packages = [$packages[0] => $this->templateRoot(). '/' . $packages[1]];
        $this->createFiles($files);

        $resolver = new TemplateResolver($path = $this->templateRoot(), $packages);

        $resolved = $resolver->resolve($template);

        $this->assertEquals(count($files), count($resolved));

        foreach ($resolved as $i => $info) {
            $this->assertInstanceof('SplFileInfo', $info);
            $this->assertSame(basename($files[$i]), $info->getFilename());
        }
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function itShouldThrowExeptionIfNoTemplateIsFound()
    {
        $resolver = new TemplateResolver($path = $this->templateRoot());
        $resolved = $resolver->resolve('some template');
    }

    /**
     * @dataProvider
     */
    public function templateProvider()
    {
        return [
            [
                ['master.php', 'master.twig'], 'master'
            ],
            [
                ['blog/master.php', 'blog/master.twig'], 'blog.master'
            ]
            ];
    }

    /**
     * @dataProvider
     */
    public function packageTemplateProvider()
    {
        return [
            [
            [
            '../vendor/package/acme/Resources/views/master.php',
            '../vendor/package/acme/Resources/views/master.twig'], 'acme:master',
            ['acme', '../vendor/package/acme']
            ],
            [
            [
            '../vendor/package/acme/Resources/views/blog/master.php',
            '../vendor/package/acme/Resources/views/blog/master.twig'], 'acme:blog.master',
            ['acme', '../vendor/package/acme']
            ]
            ];
    }

    protected function createFiles(array $files)
    {
        foreach ($files as $file) {

            $f = $this->templateRoot() . '/' . $file;
            if (!file_exists($d = dirname($f))) {
                mkdir($d, 0775, true);
            }

            touch($f);
        }
    }


    protected function templateRoot()
    {
        return $this->templateRootPath;
    }
}
