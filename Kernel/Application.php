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
use \Selene\Components\Common\Helper\ListHelper;
use \Selene\Components\Config\CacheInterface;
use \Selene\Components\Config\Loader\DelegatingLoader;
use \Selene\Components\Config\Loader\Resolver as LoaderResolver;
use \Selene\Components\Config\Resource\Locator;
use \Selene\Components\Config\Cache as ConfigCache;
use \Selene\Components\Routing\RouterInterface;
use \Selene\Components\DI\Builder;
use \Selene\Components\DI\BuilderInterface;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Processor\Processor;
use \Selene\Components\DI\Processor\Configuration;
use \Selene\Components\DI\Dumper\PhpDumper;
use \Selene\Components\DI\Dumper\ContainerGenerator;
use \Selene\Components\DI\Dumper\ContainerDumper;
use \Selene\Components\DI\Parameters;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\DI\Loader\XmlLoader;
use \Selene\Components\DI\Loader\PhpLoader;
use \Selene\Components\DI\Loader\CallableLoader;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Package\PackageRepository;
use \Selene\Components\Stack\StackBuilder as KernelStackBuilder;

/**
 * @class Application implements HttpKernelInterface, TerminableInterface, ContainerAwareInterface
 * @see HttpKernelInterface
 * @see TerminableInterface
 * @see ContainerAwareInterface
 *
 * @package Selene\Components\Kernel
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
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
     * containerCachePath
     *
     * @var void
     */
    protected $containerCachePath;

    /**
     * packageProviders
     *
     * @var array
     */
    protected $packageProviders;

    /**
     * applicationRoot
     *
     * @var string
     */
    protected $applicationRoot;

    /**
     * version
     *
     * @var string
     */
    protected static $version = '1.0.0 pre alpha';

    /**
     * testEnv
     *
     * @var string
     */
    protected static $testEnv = 'testing';

    /**
     * prodEnv
     *
     * @var string
     */
    protected static $prodEnv = 'production';

    /**
     * appServiceId
     *
     * @var string
     */
    protected static $appServiceId = 'app';

    /**
     * kernelServiceId
     *
     * @var string
     */
    protected static $kernelServiceId = 'kernel';

    /**
     * Constructor.
     *
     * @param string $environment
     * @param boolean $debug
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
     * version
     *
     * @return mixed
     */
    public static function version()
    {
        return static::$version;
    }

    /**
     * Handle a http request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $type
     * @param boolean $catch
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        try {
            $this->boot();

            return $this->getKernelStack()->handle($request, $type, $catch);

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * run
     *
     * @param Request $request
     *
     * @return void
     */
    public function run(Request $request = null, $catch = true)
    {
        $request = $request ?: Request::createFromGlobals();

        $doCatch = null !== $catch ? (bool)$catch : self::$prodEnv === $this->getEnvironment();

        $response = $this->handle($request, self::MASTER_REQUEST, $doCatch);

        $response->send();

        $this->terminate($request, $response);
    }

    /**
     * Boots the application
     *
     * Initialize packages, initialize the service container, boot the kernel
     * stack, and prepare the controller resolver.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }

        $this->initializePackages();
        $this->initializeContainer();
        $this->injectServices();
        $this->bootKernelStack();

        $this->booted = true;
    }

    /**
     * Terminates the application
     *
     * @param Request $request
     * @param Response $response
     *
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        $this->getKernelStack()->terminate($request, $response);

        if ($this->debugger) {
            $this->debugger->stop();
        }
    }

    /**
     * Get the application kernel.
     *
     * @return \Symfony\Component\HttpFoundation\HttpKernelInterface
     */
    public function getKernel()
    {
        return $this->getContainer()->get(static::$kernelServiceId);
    }

    /**
     * Get the current application envirnonment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->env;
    }

    /**
     * getApplicationServiceId
     *
     * @return string
     */
    public function getApplicationServiceId()
    {
        return static::$appServiceId;
    }

    /**
     * getContainerServiceName
     *
     * @return string
     */
    public function getContainerServiceId()
    {
        return static::$appServiceId.'.container';
    }

    /**
     * Set the Application directory
     *
     * @param mixed $path
     *
     * @return void
     */
    public function setApplicationRoot($path)
    {
        $this->applicationRoot = $path;
    }

    /**
     * Get the application root directory.
     *
     * @return string
     */
    public function getApplicationRoot()
    {
        if (null === $this->applicationRoot) {
            $this->applicationRoot = $this->guessApplicationPath();
        }

        return $this->applicationRoot;
    }

    /**
     * Set the path to the container cache.
     *
     * @param string $path
     *
     * @return void
     */
    public function setContainerCachePath($path)
    {
        $this->containerCachePath = $path;
    }

    /**
     * Get the path to the container cache.
     *
     * @return string
     */
    public function getContainerCachePath()
    {
        return $this->containerCachePath;
    }

    /**
     * Get the package repository.
     *
     * @return PackageRepository
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * Add a file path from which a package provider schold be loaded.
     *
     * @param string $path the path to the php file that returns an array of
     * package provides.
     * @param string $extension the file extension.
     *
     * @return void
     */
    public function addPackageProvider($path, $extension = '.php')
    {
        if (!file_exists($file = dirname($path).DIRECTORY_SEPARATOR.basename($path, $extension).$extension)) {
            return;
        }

        $this->packageResources[] = $file;
        $this->packageProviders[] = include $file;
    }

    /**
     * guessApplicationPath
     *
     * @return string
     */
    protected function guessApplicationPath()
    {
        $reflection = new \ReflectionObject($this);

        return dirname($reflection->getFileName());
    }

    /**
     * getKernelStack
     *
     * @return \Selene\Components\Kernel\Stack
     */
    public function getKernelStack()
    {
        return $this->getContainer()->get(static::$kernelServiceId.'.stack');
    }

    /**
     * getRequestStack
     *
     * @return mixed
     */
    public function getRequestStack()
    {
        return $this->getContainer()->get('request_stack');
    }

    /**
     * isDebugging
     *
     * @return boolean
     */
    public function isDebugging()
    {
        return null !== $this->debugger;
    }

    /**
     * runsInConsole
     *
     * @return mixed
     */
    public function runsInConsole()
    {
        return 'cli' === php_sapi_name();
    }

    /**
     * runsInTest
     *
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
     * @return mixed
     */
    public static function setTestEnvironmentName($name)
    {
        static::$testEnv = strtolower($name);
    }

    /**
     * getContainerClass
     *
     * @return string
     */
    protected function getContainerClass()
    {
        return 'Selene\Components\DI\Container';
    }

    /**
     * Inject services to the container.
     *
     * @return void
     */
    protected function injectServices()
    {
        $this->getContainer()->inject(static::$appServiceId.'.package_repository', $this->packages);
    }

    /**
     * initializeContainer
     *
     * @return void
     */
    protected function initializeContainer()
    {
        $cache = $this->getConfigCache();

        if (!$cache->isValid()) {
            $this->doBuildContainer($cache);
        }

        $this->loadContainerCache($cache);
    }

    /**
     * getConfigCache
     *
     * @return ConfigCache
     */
    protected function getConfigCache()
    {
        return new ConfigCache(
            $this->getCachedContainerFileName(),
            $this->isDebugging()
        );
    }

    /**
     * doBuildContainer
     *
     * @param ConfigCache $cache
     *
     * @return void
     */
    protected function doBuildContainer(CacheInterface $cache)
    {
        $builder = $this->buildContainer(
            $cache,
            $class = $this->getCachedContainerClassName(),
            $file  = $this->getCachedContainerFileName()
        );

        $dumper = $this->getContainerDumper(
            $builder->getContainer(),
            $this->getCachedContainerNamespace(),
            $this->getCachedContainerBaseName(),
            $this->getContainerServiceId()
        );

        $cache->write($dumper->generate(), $builder->getResources());
    }

    /**
     * getCachedContainerClassName
     *
     * @return string
     */
    protected function getCachedContainerClassName()
    {
        return sprintf(
            '%s\%s',
            $this->getCachedContainerNameSpace(),
            $this->getCachedContainerBaseName()
        );
    }

    /**
     * getCachedContainerFileName
     *
     * @return string
     */
    protected function getCachedContainerFileName()
    {
        return sprintf(
            '%s%s%s.php',
            $this->getContainerCachePath(),
            DIRECTORY_SEPARATOR,
            $this->getCachedContainerBaseName()
        );
    }


    /**
     * getCachedContainerBaseName
     *
     * @return string
     */
    protected function getCachedContainerBaseName()
    {
        return 'Container'.ucfirst($this->getEnvironment());
    }

    /**
     * getCachedContainerNameSpace
     *
     * @return string
     */
    protected function getCachedContainerNameSpace()
    {
        list($rootNs,) = explode('\\', __NAMESPACE__);

        return $rootNs.'\ClassCache';
    }

    /**
     * buildContainer
     *
     * @param ConfigCache $cache
     *
     * @return BuilderInterface
     */
    protected function buildContainer(CacheInterface $cache, $containerClass, $containerFile)
    {
        $class = $this->getContainerClass();
        $container = new $class(new Parameters($this->getDefaultParameters()));

        $this->setContainer($container);

        $container->setParameter(static::$appServiceId.'.class', get_class($this));
        $container->setParameter(static::$appServiceId.'.container.class', $containerClass);
        $container->setParameter(static::$appServiceId.'.container.file', $containerFile);
        $container->setParameter(static::$appServiceId.'.package.sources', $this->packageResources);

        $container->inject(static::$appServiceId, $this);
        $container->inject($this->getContainerServiceId(), $container);

        $builder = $this->getContainerBuilder($container);

        foreach ($this->packageResources as $file) {
            $builder->addFileResource($file);
        }

        $loader = $this->getConfigLoader(
            $builder,
            $this->getApplicationRoot().DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'packages'
        );

        $loader->load('config.xml', true);
        $loader->load('config_'.strtolower($this->getEnvironment()).'.xml', true);

        $this->packages->build($builder);

        $builder->build();

        return $builder;
    }

    /**
     * getConfigLoader
     *
     * @param BuilderInterface $builder
     * @param string $rootPath
     *
     * @return Slene\Components\Config\Resource\LoaderInterface
     */
    protected function getConfigLoader(BuilderInterface $builder, $rootPath)
    {
        $locator = new Locator($this->getPackageConfig(), $rootPath);

        $resolver = new LoaderResolver([
          new XmlLoader($builder, $locator),
          new PhpLoader($builder, $locator),
          new CallableLoader($builder)
        ]);

        return new DelegatingLoader($resolver);
    }

    /**
     * getContainerBuilder
     *
     * @param ContainerInterface $container
     *
     * @return BuilderInterface
     */
    protected function getContainerBuilder(ContainerInterface $container)
    {
        return new Builder($container, new Processor(new Configuration));
    }

    /**
     * getContainerDumper
     *
     * @param ContainerInterface $container
     * @param string $namespace
     * @param string $className
     * @param string $id
     *
     * @return PhpDumper
     */
    protected function getContainerDumper(ContainerInterface $container, $namespace, $className, $id)
    {
        return new ContainerGenerator($container, $namespace, $className, $id);
        //file_put_contents('/Users/malcolm/container.php.dist', $gen->generate());
        //die;
        //return new PhpDumper($container, $namespace, $className, $id);
    }

    /**
     * loadContainerCache
     *
     * @return void
     */
    protected function loadContainerCache(CacheInterface $cache)
    {
        include $cache->getFile();

        $class = $this->getCachedContainerClassName();
        $container = new $class;

        $this->setContainer($container);
    }

    /**
     * getDefaultParameters
     *
     * @return array
     */
    protected function getDefaultParameters()
    {
        $appid = static::$appServiceId;
        $kerid = static::$kernelServiceId;

        return array_merge(
            $this->getPackageInfo($appid),
            [
                $kerid.'.root'          => $this->getApplicationRoot(),
                $appid.'.root'          => $this->getApplicationRoot(),
                $appid.'.env'           => $this->getEnvironment(),
                $appid.'.debugging'     => null !== $this->debugger,
            ]
        );
    }

    /**
     * getPackageInfo
     *
     * @return array
     */
    protected function getPackageInfo($key)
    {
        $info = [];
        $path = [];

        foreach ($this->getPackages() as $alias => $package) {
            $info[$alias] = $package->getNamespace();
            $path[$alias] = $package->getPath();
        }

        return [
            $key.'.packages' => $info,
            $key.'.package_paths' => $path
        ];
    }

    /**
     * bootKernelStack
     *
     * @return mixed
     */
    protected function bootKernelStack()
    {
        $builder = $this->getContainer()->get(static::$kernelServiceId.'.stackbuilder');

        $this->container->inject(static::$kernelServiceId.'.stack', $stack = $builder->make());
    }

    /**
     * registerPackages
     *
     * @return void
     */
    protected function registerPackages()
    {
        $this->packages->register($this->container);
    }

    /**
     * initializePackages
     *
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
     * @return array
     */
    protected function getPackageResources()
    {
        $paths = ListHelper::arrayFlatten($this->packageProviders);

        return $paths;
    }
}
