<?php

/**
 * This File is part of the Selene\Module\DI\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI\Tests\Loader;


use \Mockery as m;
use \Selene\Module\DI\Builder;
use \Selene\Module\DI\Container;
use \Selene\Module\DI\Loader\XmlLoader;
use \Selene\Module\Config\Resource\Locator;

/**
 * @class XmlLoaderTest
 * @package Selene\Module\DI\Tests\Loader
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected $loader;
    protected $builder;
    protected $container;

    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $container = m::mock('\Selene\Module\DI\ContainerInterface');
        $builder = m::mock('\Selene\Module\DI\BuilderInterface');
        $locator   = m::mock('\Selene\Module\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        $loader = new XmlLoader($builder, $locator);
        $this->assertInstanceof('\Selene\Module\DI\Loader\XmlLoader', $loader);
    }

    /** @test */
    public function itShouldSupportXml()
    {
        $loader = $this->getLoaderMock();

        $this->assertTrue($loader->supports('some/file.xml'));
        $this->assertFalse($loader->supports('some/file.php'));
    }

    /** @test */
    public function itShouldParseParameters()
    {
        $this->newLoader();
        $this->loader->load('services.0.xml');

        $this->assertTrue($this->container->hasParameter('foo'));
        $this->assertSame('bar', $this->container->getParameter('foo'));
    }

    /** @test */
    public function itShouldParseServices()
    {
        $this->newLoader();

        $this->loader->load('services.1.xml');

        $this->assertTrue($this->container->hasDefinition('foo_service'));
        $this->assertTrue($this->container->hasDefinition('foo_alias'));
        $this->assertSame('\stdClass', $this->container->getDefinition('foo_service')->getClass());

        $this->loader->load('services.1.1.xml');

        $this->assertTrue($this->container->hasDefinition('bar_service'));
        $this->assertTrue($this->container->getDefinition('bar_service')->isInjected());

        $this->assertTrue($this->container->hasDefinition('bar_child'));
        $this->assertSame('bar_service', $this->container->getDefinition('bar_child')->getParent());

        $this->assertTrue($this->container->hasDefinition('factory_service'));
        $this->assertTrue($this->container->getDefinition('factory_service')->hasFactory());
    }

    /** @test */
    public function itShouldParseImportedFiles()
    {
        $this->newLoader();

        $this->loader->load('services.2.xml');

        $this->assertSame([['foo' => 'bar']], $this->builder->getPackageConfig('acme'));
    }

    /** @test */
    public function itShouldAddResourcesToTheBuilder()
    {
        $dir = __DIR__.'/Fixures';
        $this->newLoader();

        $this->loader->load('services.2.xml', true);

        $resources = $this->builder->getResources();

        $this->assertSame(2, count($resources));

        $this->assertTrue(in_array($dir.DIRECTORY_SEPARATOR.'services.2.xml', $resources));
        $this->assertTrue(in_array($dir.DIRECTORY_SEPARATOR.'imported.0.xml', $resources));
    }

    /** @test */
    public function isShouldParseImportedPackageConfig()
    {
        $this->newLoader();

        $this->loader->load('services.2.xml');

        $this->assertSame([['foo' => 'bar']], $this->builder->getPackageConfig('acme'));

        $this->loader->load('services.2.1.xml');

        $this->assertSame([], $this->builder->getPackageConfig(''));
    }

    /** @test */
    public function itShouldLoadAllFilesWhenRequested()
    {
        $loader = new XmlLoader(
            $builder = new Builder(
                $container = new Container
            ),
            new Locator(
                [
                    $dirA = __DIR__.DIRECTORY_SEPARATOR.'Fixures',
                    $dirB = __DIR__.DIRECTORY_SEPARATOR.'Fixures' . DIRECTORY_SEPARATOR . 'sub'
                ]
            )
        );

        $loader->load('services.0.xml', true);

        $resources = $builder->getResources();

        $this->assertSame(2, count($resources));

        $this->assertSame($dirA.DIRECTORY_SEPARATOR.'services.0.xml', (string)$resources[0]);
        $this->assertSame($dirB.DIRECTORY_SEPARATOR.'services.0.xml', (string)$resources[1]);
    }

    /** @test */
    public function itShouldParseParmetersArray()
    {
        $this->newLoader();

        $this->loader->load('services.3.xml', false);

        $this->assertTrue($this->container->hasParameter('nested'));

        $params = $this->container->getParameter('nested');

        $expected = [
            'a',
            'b',
            'nested' => [1, 2, 'bar' => 'baz'],
            'foo' => 'bar'
        ];

        $this->assertSame($expected, $params);
    }

    /** @test */
    public function itShouldParseMetaDataInDefinitions()
    {
        $this->newLoader();

        $this->loader->load('services.4.xml', false);

        $def = $this->container->getDefinition('test_listener');

        $this->assertTrue($def->hasMetaData('app_events'));
    }

    /** @test */
    public function itiShoultThrowExceptionIfClassIsMissing()
    {
        $this->newLoader();

        try {
            $this->loader->load('errored.0.xml', false);
        } catch (\InvalidArgumentException $e) {
            $this->assertSame(
                'Definition "foo_service" must define its class unless it has a parent definition',
                $e->getMessage()
            );
            return;
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->fail('test splipped');
    }

    /** @test */
    public function itShouldSetSetters()
    {
        $this->newLoader();
        $this->loader->load('services.6.xml', false);

        $def = $this->container->getDefinition('foo_service');

        $this->assertTrue($def->hasSetters());

        $setters = $def->getSetters();

        $this->assertSame('setFoo', key($setters[0]));
        $this->assertSame(['foo'], $setters[0][key($setters[0])]);
    }

    /** @test */
    public function itShouldConvertToParentDefinition()
    {
        $this->newLoader();
        $this->loader->load('services.5.xml', false);

        $this->assertInstanceof(
            'Selene\Module\DI\Definition\ParentDefinition',
            $this->container->getDefinition('concrete')
        );
    }

    /** @test */
    public function itShouldReplaceParentArguments()
    {
        $this->newLoader();
        $this->loader->load('services.5.xml', false);

        $def = $this->container->getDefinition('concrete');

        $args = $def->getArguments();

        $this->assertTrue(isset($args['index_1']));
    }

    protected function getLoaderMock($builder = null, $container = null, $locator = null)
    {
        $builder =   $builder = $builder ?: m::mock('\Selene\Module\DI\BuilderInterface');
        $container = m::mock('\Selene\Module\DI\ContainerInterface');
        $locator   = $locator ?: m::mock('\Selene\Module\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        return  new XmlLoader($builder, $locator);
    }

    protected function newLoader()
    {
        $this->loader = new XmlLoader(
            $this->builder = new Builder($this->container = new Container),
            new Locator([$dir = __DIR__.'/Fixures'])
        );
    }
}
