<?php

/**
 * This File is part of the Selene\Components\Console package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Console;

use \Psr\Log\LogLevel;
use \Psr\Log\LoggerInterface;
use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\Kernel\ApplicationInterface;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\Console\Input\ArrayInput;
use \Symfony\Component\Console\Output\NullOutput;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Command\Command as SymfonyCommand;
use \Symfony\Component\Console\Application as ConsoleApplication;
use \Symfony\Component\EventDispatcher\EventDispatcherInterface;
use \Symfony\Component\Console\Logger\ConsoleLogger;
use \Symfony\Component\Console\Output\ConsoleOutput;

/**
 * @class Application extends ConsoleApplication
 * @see ConsoleApplication
 *
 * @package Selene\Components\Console
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Application extends ConsoleApplication
{
    /**
     * events
     *
     * @var DispatcherInterface
     */
    private $dispatcher;

    /**
     * events
     *
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(HttpKernelInterface $app, $name, $version)
    {
        parent::__construct($name, $version);

        $this->app = $app;
        $this->initialize($app);
    }

    /**
     * setEvents
     *
     * @param DispatcherInterface $events
     *
     * @return void
     */
    public function setEvents(DispatcherInterface $events)
    {
        $this->dispatcher = $events;
    }

    /**
     * getEvents
     *
     * @return void
     */
    public function getEvents()
    {
        return $this->dispatcher;
    }

    /**
     * setDispatcher
     *
     * @param EventDispatcherInterface $dispatcher
     *
     * @return void
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
    }

    /**
     * setLogger
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * getVerbosityLevels
     *
     * @return array
     */
    public function getVerbosityLevels()
    {
        return [
            LogLevel::NOTICE => OutputInterface::VERBOSITY_VERBOSE
        ];
    }

    /**
     * getFormatLevels
     *
     * @access public
     * @return void
     */
    public function getFormatLevels()
    {
        return [
        ];
    }

    /**
     * getLogger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    private function newLogger()
    {
        $this->logger = new ConsoleLogger();
    }

    /**
     * getApplication
     *
     * @access public
     * @return ApplicationInterface
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * initialize
     *
     * @param mixed $app
     *
     * @access protected
     * @return void
     */
    protected function initialize($app)
    {
        $app->boot();

        foreach ($app->getPackages() as $package) {
            $package->registerCommands($this);
        }
    }

    /**
     * add
     *
     * @param SymfonyCommand $cmd
     *
     * @return void
     */
    public function add(SymfonyCommand $cmd)
    {
        return parent::add($cmd);
    }
}
