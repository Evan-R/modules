<?php

/**
 * This File is part of the Selene\Components\DI\Tests\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Tests\Loader;


use \Mockery as m;
use \Selene\Components\DI\Builder;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\Config\Resource\Locator;

/**
 * @class XmlLoaderTest
 * @package Selene\Components\DI\Tests\Loader
 * @version $Id$
 */
class XmlLoaderTest extends \PHPUnit_Framework_TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @test
     */
    public function itShouldBeInstantiable()
    {
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $builder = m::mock('\Selene\Components\DI\BuilderInterface');
        $locator   = m::mock('\Selene\Components\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        $loader = new XmlLoader($builder, $locator);
        $this->assertInstanceof('\Selene\Components\DI\Loader\XmlLoader', $loader);
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
        $loader = new XmlLoader(new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.0.xml');

        $this->assertTrue($container->hasParameter('foo'));
        $this->assertSame('bar', $container->getParameter('foo'));
    }

    /** @test */
    public function itShouldParseServices()
    {

        $loader = new XmlLoader(new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.1.xml');

        $this->assertTrue($container->hasDefinition('foo_service'));
        $this->assertTrue($container->hasDefinition('foo_alias'));
        $this->assertSame('StdClass', $container->getDefinition('foo_service')->getClass());

        $loader->load('services.1.1.xml');

        $this->assertTrue($container->hasDefinition('bar_service'));
        $this->assertTrue($container->getDefinition('bar_service')->isInjected());

        $this->assertTrue($container->hasDefinition('bar_child'));
        $this->assertSame('bar_service', $container->getDefinition('bar_child')->getParent());

        $this->assertTrue($container->hasDefinition('factory_service'));
        $this->assertTrue($container->getDefinition('factory_service')->hasFactory());
    }

    /** @test */
    public function itShouldParseImportedFiles()
    {

        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([__DIR__.'/Fixures']));

        $loader->load('services.2.xml');

        $this->assertSame([['foo' => 'bar']], $builder->getPackageConfig('acme'));
    }

    /** @test */
    public function itShouldAddResourcesToTheBuilder()
    {
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        $loader->load('services.2.xml');

        $resources = $builder->getResources();

        $this->assertSame(2, count($resources));

        $this->assertSame($dir.DIRECTORY_SEPARATOR.'services.2.xml', (string)$resources[0]);
        $this->assertSame($dir.DIRECTORY_SEPARATOR.'imported.0.xml', (string)$resources[1]);
    }


    /** @test */
    public function isShouldParseImportedPackageConfig()
    {
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        $loader->load('services.2.xml');

        $this->assertSame([['foo' => 'bar']], $builder->getPackageConfig('acme'));

        $loader->load('services.2.1.xml');

        $this->assertSame([], $builder->getPackageConfig(''));
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
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        $loader->load('services.3.xml', false);

        $this->assertTrue($container->hasParameter('nested'));

        $params = $container->getParameter('nested');

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
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        $loader->load('services.4.xml', false);

        $def = $container->getDefinition('test_listener');

        $this->assertTrue($def->hasMetaData('app_events'));
    }

    /** @test */
    public function itiShoultThrowExceptionIfClassIsMissing()
    {

        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));

        try {
            $loader->load('errored.0.xml', false);
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
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));
        $loader->load('services.6.xml', false);

        $def = $container->getDefinition('foo_service');

        $this->assertTrue($def->hasSetters());

        $setters = $def->getSetters();

        $this->assertSame('setFoo', key($setters[0]));
        $this->assertSame(['foo'], $setters[0][key($setters[0])]);
    }

    /** @test */
    public function itShouldConvertToParentDefinition()
    {
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));
        $loader->load('services.5.xml', false);

        $this->assertInstanceof(
            'Selene\Components\DI\Definition\ParentDefinition',
            $container->getDefinition('concrete')
        );
    }

    /** @test */
    public function itShouldReplaceParentArguments()
    {
        $loader = new XmlLoader($builder = new Builder($container = new Container), new Locator([$dir = __DIR__.'/Fixures']));
        $loader->load('services.5.xml', false);

        $def = $container->getDefinition('concrete');

        $args = $def->getArguments();

        $this->assertTrue(isset($args['index_1']));
    }

    protected function getLoaderMock($builder = null, $container = null, $locator = null)
    {
        $builder =   $builder = $builder ?: m::mock('\Selene\Components\DI\BuilderInterface');
        $container = m::mock('\Selene\Components\DI\ContainerInterface');
        $locator   = $locator ?: m::mock('\Selene\Components\Config\Resource\LocatorInterface');

        $builder->shouldReceive('getContainer')->andReturn($container);

        return  new XmlLoader($builder, $locator);
    }
}
