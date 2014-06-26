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

use \Selene\Components\Events\DispatcherInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Symfony\Component\Console\Helper\TableHelper;
use \Symfony\Component\Console\Input\ArrayInput;
use \Symfony\Component\Console\Output\NullOutput;
use \Symfony\Component\Console\Input\InputOption;
use \Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Component\Console\Input\InputInterface;
use \Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * @class Command
 * @package Selene\Components\Console
 * @version $Id$
 */
class Command extends SymfonyCommand
{
    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * description
     *
     * @var string
     */
    protected $description;

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * getDescription
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * getApp
     *
     * @return Application
     */
    public function getApp()
    {
        return $this->getApplication()->getApplication();
    }

    /**
     * run
     *
     * @return mixed
     */
    protected function fire()
    {
    }

    /**
     * execute
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $this->setEventHandlers();

        return $this->fire();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->getName());
        $this->setDescription($this->getDescription());

        $this->setArguments();
        $this->setOptions();

        $this->postConfigure();
    }

    protected function postConfigure()
    {
    }

    /**
     * getInput
     *
     * @return InputInterface
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * getOutput
     *
     * @return OutputInterface
     */
    protected function getOutput()
    {
        return $this->output;
    }

    /**
     * getContainer
     *
     * @access public
     * @return mixed
     */
    public function getContainer()
    {
        if (($app = $this->getApp()) instanceof ContainerAwareInterface) {
            return $app->getContainer();
        }

        throw new \BadMethodCallException();
    }

    /**
     * setColor
     *
     * @param string $value
     * @param string $fg
     * @param string $bg
     *
     * @return string
     */
    public function setColor($value, $fg, $bg = null)
    {
        if (null !== $bg) {
            return sprintf('<fg=%s bg=%s>%s</fg=%s bg=%s>', $fg, $bg, $value, $fg, $bg);
        }

        return sprintf('<fg=%s>%s</fg=%s>', $fg, $value, $fg);
    }

    /**
     * createTable
     *
     * @param array $header
     * @param array $rows
     * @param mixed $layout
     *
     * @access public
     * @return TableHelper
     */
    public function createTable(array $header, array $rows = [], $layout = TableHelper::LAYOUT_BORDERLESS)
    {
        $table = new TableHelper();
        $table->setHeaders($header);
        $table->setRows($rows);
        $table->setLayout($layout);

        return $table;
    }

    protected function setArguments()
    {
        foreach ((array)$this->getArguments() as $option) {
            if (!is_array($option)) {
                continue;
            }

            list ($name, $mode, $description) = $option;

            $this->addArgument($name, $mode, $description);
        }
    }

    protected function setOptions()
    {
        foreach ((array)$this->getOptions() as $option) {
            if (!is_array($option)) {
                continue;
            }

            list ($name, $default, $type, $description) = $option;

            $this->addOption($name, $default, $type, $description);
        }
    }

    /**
     * setEventHandlers
     *
     * @return void
     */
    protected function setEventHandlers()
    {
        if (!$events = $this->getEvents()) {
            return;
        }

        foreach ((array)$this->getEventHandlers() as $event => $handler) {
            foreach ((array)$handler as $eventHandler) {
                if (is_callable($eventHandler)) {
                    $events->on($event, $eventHandler);
                }
            }
        }
    }

    /**
     * getArguments
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * getOptions
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }

    /**
     * getEventHandlers
     *
     * @access protected
     * @return array
     */
    protected function getEventHandlers()
    {
        return [];
    }

    /**
     * getEvents
     *
     *
     * @access protected
     * @return DispatcherInterface
     */
    protected function getEvents()
    {
        if ($app = $this->getApplication()) {
            return $app->getEvents();
        }
    }

    /**
     * getLogger
     *
     * @return LoggetInterface
     */
    protected function getLogger()
    {
        if ($app = $this->getApplication()) {
            return $app->getLogger();
        }
    }
}
