<?php

/**
 * This File is part of the Selene\Components\Kernel package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\HttpKernel\TerminableInterface;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Routing\RouterInterface;
use \Selene\Components\DI\Builder;
use \Selene\Components\DI\Dumper\ContainerDumper;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\Config\Cache as ConfigCache;
use \Selene\Components\Kernel\KernelStack;
use \Selene\Components\Kernel\StackBuilder as KernelStackBuilder;
use \Selene\Components\Package\PackageRepository;
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\Config\Resource\LoaderResolver;
use \Selene\Components\Config\Resource\DelegatingLoader;
use \Selene\Components\DI\Loader\XmlLoader;

/**
 * @class Application implements HttpKernelInterface, TerminableInterface, ContainerAwareInterface
 * @see HttpKernelInterface
 * @see TerminableInterface
 * @see ContainerAwareInterface
 *
 * @package Selene\Components\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Application implements HttpKernelInterface, TerminableInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * debugger
     *
     * @var Selene\Components\Kernel\Debugger
     */
    protected $debugger;

    /**
     * configLoader
     *
     * @var mixed
     */
    protected $configLoader;

    /**
     * booted
     *
     * @var boolean
     */
    protected $booted;

    /**
     * packages
     *
     * @var \Selene\Components\Package\PackageRepository
     */
    protected $packages;

    /**
     * @param mixed $environment
     * @param mixed $debug
     *
     * @access public
     * @return mixed
     */
    public function __construct($environment, $debug = true)
    {
        $this->env = $environment;

        if ((bool)$debug) {
            $this->debugger = new Debugger;
            $this->debugger->start();
        }
    }

    /**
     * handle
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $type
     * @param boolean $catch
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        try {
            $this->boot();
            $response = $this->getKernelStack()->handle($request, $type, $catch);

        } catch (\Exception $e) {
            throw $e;
            return;
        }
        return $response;
    }

    /**
     * boot
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->initializePackages();
        $this->initializeContainer();

        $this->bootKernelStack();

        $this->booted = true;
    }

    /**
     * getContainerServiceName
     *
     * @access public
     * @return string
     */
    public function getContainerServiceId()
    {
        return 'app.container';
    }

    /**
     * terminate
     *
     * @param Request $request
     * @param Response $response
     *
     * @access public
     * @return mixed
     */
    public function terminate(Request $request, Response $response)
    {
        if ($this->debugger) {
            $this->debugger->stop();
        }
    }

    /**
     * getKernel
     *
     * @access protected
     * @return \Symfony\Component\HttpFoundation\HttpKernelInterface
     */
    public function getKernel()
    {
        return $this->getContainer()->get('app_kernel');
    }

    /**
     * getKernelStack
     *
     * @access public
     * @return \Selene\Components\Kernel\Stack
     */
    public function getKernelStack()
    {
        return $this->getContainer()->get('app_kernel.stack');
    }

    /**
     * getRequestStack
     *
     * @access public
     * @return mixed
     */
    public function getRequestStack()
    {
        return $this->getContainer()->get('request_stack');
    }

    /**
     * isDebugging
     *
     * @access public
     * @return boolean
     */
    public function isDebugging()
    {
        return null !== $this->debugger;
    }

    /**
     * loadConfig
     *
     * @access protected
     * @return mixed
     */
    protected function loadConfig()
    {

    }

    /**
     * getContainerClass
     *
     * @access protected
     * @return string
     */
    protected function getContainerClass()
    {
        return 'Selene\Components\DI\BaseContainer';
    }

    /**
     * initializeContainer
     *
     * @access protected
     * @return void
     */
    protected function initializeContainer()
    {
        $cache = new ConfigCache(
            $file = $this->getContainerCachePath() . '/Container'.ucfirst($this->env).'.php',
            $this->isDebugging()
        );

        if ($cache->isValid()) {
            return $this->loadContainerCache($config);
        }

        $this->buildContainer($cache);

        $cache->write('some nonesense', $this->container->getResources()->toArray());
    }

    /**
     * buildContainer
     *
     * @param ConfigCache $cache
     *
     * @access protected
     * @return void
     */
    protected function buildContainer(ConfigCache $cache)
    {
        $builder = new Builder(new ContainerDumper);
        $builder->setContainerClass($this->getContainerClass());

        $builder->build(function ($container) {

            $this->setContainer($container);
            $this->container->setParameter('app.root', $this->getApplicationRoot());
            $this->container->inject($this->getContainerServiceId(), $container);

            $configPaths = $this->getPackageConfig();

            $locator = new Locator($configPaths);

            $locator->setRootPath(
                $this->getApplicationRoot().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'packages'
            );
            $resolver = new LoaderResolver([
                new XmlLoader($this->container, $locator)
            ]);
            $loader = new DelegatingLoader($resolver);

            $loader->load('config.xml');

            $this->packages->build($container);
        });

        $this->container->compile();
    }

    /**
     * loadContainerCache
     *
     * @access protected
     * @return void
     */
    protected function loadContainerCache(ConfigCache $cache)
    {
        $file = (string)$cache;

        $container = (basename($file));
        $this->setContainer($container);
    }

    /**
     * getDefaultParameters
     *
     * @access protected
     * @return mixed
     */
    protected function getDefaultParameters()
    {
        return [
            'app_kernel.root' => $this->getApplicationRoot()
        ];
    }

    /**
     * bootKernelStack
     *
     * @access protected
     * @return mixed
     */
    protected function bootKernelStack()
    {
        $builder = new KernelStackBuilder($this->getKernel());
        $this->packages->registerMiddlewares($builder);

        $this->container->inject('app_kernel.stack', $stack = $builder->make());
    }

    /**
     * registerPackages
     *
     * @access protected
     * @return void
     */
    protected function registerPackages()
    {
        $this->packages->register($this->container);
    }

    /**
     * initializePackages
     *
     * @access protected
     * @return void
     */
    protected function initializePackages()
    {
        $this->packages = $this->packages ?:
            new PackageRepository($this->initPackages($this->getPackages()));
    }

    protected function getPackageConfig()
    {
        $paths = [];
        foreach ($this->packages as $alias => $package) {
            $paths[] = $alias;
        }

        return $paths;
    }

    protected function initPackages(array $packages)
    {
        $initialized = [];

        foreach ($packages as $packageClass) {
            if (class_exists($packageClass)) {
                $initialized[] = new $packageClass;
            }
        }

        return $initialized;
    }


    /**
     * getPackages
     *
     *
     * @access protected
     * @abstract
     * @return array
     */
    abstract protected function getPackages();

    /**
     * getContainerCachePath
     *
     *
     * @access protected
     * @abstract
     * @return string
     */
    abstract protected function getContainerCachePath();
}
