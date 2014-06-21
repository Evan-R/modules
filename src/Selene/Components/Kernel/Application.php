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
use \Selene\Components\DI\Parameters;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\Config\Cache as ConfigCache;
use \Selene\Components\Stack\StackBuilder as KernelStackBuilder;
use \Selene\Components\Package\PackageRepository;
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\Config\Resource\LoaderResolver;
use \Selene\Components\Config\Resource\DelegatingLoader;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\DI\Loader\PhpLoader;
use \Selene\Components\DI\Loader\CallableLoader;
use \Selene\Components\Common\Helper\ListHelper;

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
    public $debugger;

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

    /**
     * packageProviders
     *
     * @var array
     */
    protected $packageProviders;

    /**
     * testEnv
     *
     * @var string
     */
    protected static $testEnv = 'testing';

    protected static $version = '1.0.0 α';

    /**
     * Create a new Application instance.
     *
     * @param mixed $environment
     * @param mixed $debug
     *
     * @access public
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
     * Handle a http request.
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
     * Boots the application
     *
     * Initialize packages, initialize the service container, boot the kernel
     * stack, and prepare the controller resolver.
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
     * Set the Application directory
     *
     * @param mixed $path
     *
     * @access public
     * @return voi
     */
    public function setApplicationRoot($path)
    {
        $this->applicationRoot = $path;
    }

    /**
     * Get the application directory.
     *
     * @access public
     * @return string
     */
    public function getApplicationRoot()
    {
        if (null === $this->applicationRoot) {
            $reflection = new \ReflectionObject($this);
            $this->applicationRoot = dirname($reflection->getFile());
        }

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
     * runsInConsole
     *
     * @access public
     * @return mixed
     */
    public function runsInConsole()
    {
        return 'cli' === php_sapi_name();
    }

    /**
     * runsInTest
     *
     * @access public
     * @return mixed
     */
    public function runsInTest()
    {
        return static::$testEnv === $this->getEnvironment();
    }

    /**
     * setTestEnvironmentName
     *
     * @param mixed $name
     *
     * @access public
     * @return mixed
     */
    public static function setTestEnvironmentName($name)
    {
        static::$testEnv = strtolower($name);
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
        $className = 'Container'.ucfirst($this->getEnvironment());

        $cache = new ConfigCache(
            $file = $this->getContainerCachePath() . DIRECTORY_SEPARATOR . $className.'.php',
            $this->isDebugging()
        );

        $class = $ns . '\\' . $className;

        //if (true) {
        if (!$cache->isValid()) {

            $builder = $this->buildContainer($cache, $class, $file);
            $dumper = new PhpDumper($this->container, $ns, $className, $this->getContainerServiceId());

            $cache->write($dumper->dump(), $builder->getResources());

        }

        return $this->loadContainerCache($class, $file);
    }

    /**
     * buildContainer
     *
     * @param ConfigCache $cache
     *
     * @access protected
     * @return void
     */
    protected function buildContainer(ConfigCache $cache, $containerClass, $containerFile)
    {

        $class = $this->getContainerClass();
        $container = new $class(new Parameters($this->getDefaultParameters()));


        $this->setContainer($container);

        $this->container->setParameter('app_container.class', $containerClass);
        $this->container->setParameter('app_container.file', $containerFile);
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
            new XmlLoader($builder, $locator),
            new PhpLoader($builder, $locator),
            new CallableLoader($builder, $locator)
        ]);

        $loader = new DelegatingLoader($resolver);

        $loader->load('config.xml', true);

        $loader->load('config_'.strtolower($this->getEnvironment()).'.xml', true);

        $builder->configure();

        $this->packages->build($builder);

        $builder->build();

        return $builder;
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
        list($packages, $packagePaths) = $this->getPackageInfo();

        return [
            'app_kernel.root'   => $this->getApplicationRoot(),
            'app.root'          => $this->getApplicationRoot(),
            'app.packages'      => $packages,
            'app.package_paths' => $packagePaths,
            'app.env'           => $this->getEnvironment()
        ];
    }

    public function getEnvironment()
    {
        return $this->env;
    }

    protected function getPackageInfo()
    {
        $info = [];
        $path = [];

        foreach ($this->getLoadedPackages() as $alias => $package) {
            $info[$alias] = $package->getNamespace();
            $path[$alias] = $package->getPath();
        }

        return [$info, $path];
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

    /**
     * getPackageConfig
     *
     * @access protected
     * @return array
     */
    protected function getPackageConfig()
    {
        $paths = [];
        foreach ($this->packages as $alias => $package) {
            $paths[] = $alias;
        }

        return $paths;
    }

    /**
     * initPackages
     *
     * @param array $packages
     *
     * @access protected
     * @return array
     */
    protected function initPackages(array $packages)
    {
        $initialized = [];

        foreach ($packages as $packageClass) {
            if (class_exists($packageClass)) {
                $initialized[] = new $packageClass;
                continue;
            }
            throw new \InvalidArgumentException(
                sprintf('package class "%s" does not exist', $packageClass)
            );
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
    protected function getPackageResources()
    {
        $paths = ListHelper::arrayFlatten($this->packageProviders);
        return $paths;
    }

    public static function version()
    {
        return static::$version;
    }
}
