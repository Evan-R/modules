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
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

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
class Application implements HttpKernelInterface, TerminableInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * denug
     *
     * @var array
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
     * @var mixed
     */
    protected $booted;

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
     * @param Request $request
     * @param mixed $type
     * @param mixed $catch
     *
     * @access public
     * @return mixed
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->getRequestStack()->push($request);

        try {
            $this->boot();
            $response = $this->getKernelStack()->handle($request, $type, $catch);

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }

        return $response;
    }

    /**
     * boot
     *
     *
     * @access public
     * @return mixed
     */
    public function boot()
    {
        if ($this->booted) {
            return;
        }
    }

    /**
     * getApplicationStack
     *
     * @access protected
     * @return mixed
     */
    protected function getKernelStack()
    {
        return $this->getContainer()->get('app_kernel.stack');
    }

    /**
     * initialize
     *
     * @access public
     * @return mixed
     */
    public function initialize()
    {
        $this->loadConfig();
    }

    /**
     * initIalizeContainer
     *
     * @access public
     * @return mixed
     */
    public function getContainerConfig()
    {
        return;
    }

    /**
     * getConfigLoader
     *
     * @access public
     * @return mixed
     */
    public function getConfigLoader()
    {
        if (null === $this->configLoader) {

        }

        return $this->configLoader();
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
        return null;
    }

    /**
     * getKernel
     *
     * @access protected
     * @return mixed
     */
    public function getKernel()
    {
        return $this->getContainer()->get('app_kernel');
    }

    public function getRequestStack()
    {
        return $this->getContainer()->get('request_stack');
    }
}
