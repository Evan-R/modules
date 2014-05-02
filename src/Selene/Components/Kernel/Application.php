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
use \Selene\Components\DI\Processor\Processor;
use \Selene\Components\DI\Dumper\PhpDumper;
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
class Application implements ApplicationInterface, HttpKernelInterface, TerminableInterface, ContainerAwareInterface
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
     * includePhpResources
     *
     * @var array
     */
    protected $packageResources;
    protected $packageProviders;

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

        $this->packageResources = [];
        $this->packageProviders = [];
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
        return 'app_container';
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
        return $this->getContainer()->get('kernel_stack');
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
        return 'Selene\Components\DI\Container';
    }

    /**
     * initializeContainer
     *
     * @access protected
     * @return void
     */
    protected function initializeContainer()
    {
        $ns = 'Selene\ClassCache';
        $className = 'Container'.ucfirst($this->env);

        $cache = new ConfigCache(
            $file = $this->getContainerCachePath() . DIRECTORY_SEPARATOR . $className.'.php',
            $this->isDebugging()
        );


        if ($cache->isValid()) {
            //$className = $className . '_' . hash('md5', filemtime($file));
            $class = $ns . '\\' . $className;
            return $this->loadContainerCache($class, $file);
        }

        //$className = $className . '_' . hash('md5', $time = time());

        $builder = $this->buildContainer($cache);
        $dumper = new PhpDumper($this->container, $ns, $className, $this->getContainerServiceId());

        $cache->write($dumper->dump(), $builder->getResources()->toArray());
        //touch($file, $time);
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

        $class = $this->getContainerClass();
        $container = new $class;

        $this->setContainer($container);
        $this->container->setParameter('app.root', $this->getApplicationRoot());
        $this->container->inject($this->getContainerServiceId(), $container);

        $builder = new Builder($container, new Processor);

        foreach ($this->packageResources as $file) {
            $builder->addFileResource($file);
        }

        $configPaths = $this->getPackageConfig();

        $locator = new Locator($configPaths);

        $locator->setRootPath(
            $this->getApplicationRoot().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'packages'
        );
        $resolver = new LoaderResolver([
            new XmlLoader($builder, $locator)
        ]);
        $loader = new DelegatingLoader($resolver);

        $loader->load('config.xml');

        $this->packages->build($builder);

        $builder->build();


        return $builder;

        //$this->container->compile();

    }

    /**
     * loadContainerCache
     *
     * @access protected
     * @return void
     */
    protected function loadContainerCache($class, $file)
    {
        include $file;

        $container = new $class;

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
        $builder = $this->getContainer()->get('kernel_stackbuilder');
        $this->container->inject('kernel_stack', $stack = $builder->make());
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
            new PackageRepository($this->initPackages($this->getPackageResources()));
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
     * setApplicationRoot
     *
     * @param mixed $path
     *
     * @access public
     * @return mixed
     */
    public function setApplicationRoot($path)
    {
        $this->applicationRoot = $path;
    }

    /**
     * getApplicationRoot
     *
     *
     * @access public
     * @return mixed
     */
    public function getApplicationRoot()
    {
        return $this->applicationRoot;
    }

    /**
     * setContainerCachePath
     *
     * @param mixed $path
     *
     * @access public
     * @return void
     */
    public function setContainerCachePath($path)
    {
        $this->containerCachePath = $path;
    }

    /**
     * getContainerCachePath
     *
     * @access public
     * @return string
     */
    public function getContainerCachePath()
    {
        return $this->containerCachePath;
    }

    /**
     * getLoadedPackages
     *
     * @access public
     * @return PackageRepository
     */
    public function getLoadedPackages()
    {
        return $this->packages;
    }

    public function addPackageProvider($path, $extension = 'php', $default = [])
    {
        if (file_exists($file = $path . '.' . $extension)) {
            $this->packageResources[] = $file;
            $this->packageProviders[] = include $file;
            return;
        }

    }

    /**
     * getPackages
     *
     *
     * @access protected
     * @abstract
     * @return array
     */
    protected function getPackageResources()
    {
        $paths = arrayFlatten($this->packageProviders);
        return $paths;
    }
}
