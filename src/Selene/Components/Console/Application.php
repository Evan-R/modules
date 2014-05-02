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

use \Selene\Components\Kernel\ApplicationInterface;
use \Symfony\Component\HttpKernel\HttpKernelInterface;
use \Symfony\Component\Console\Input\ArrayInput;
use \Symfony\Component\Console\Output\NullOutput;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Command\Command as SymfonyCommand;
use \Symfony\Component\Console\Application as ConsoleApplication;

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
    public function __construct(HttpKernelInterface $app, $name, $version)
    {
        parent::__construct($name, $version);

        $this->app = $app;
        $this->initialize($app);
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

        foreach ($app->getLoadedPackages() as $package) {
            $package->registerCommands($this);
        }
    }

    public function add(SymfonyCommand $cmd)
    {
        return parent::add($cmd);
    }
}
