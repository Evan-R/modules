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

use \Selene\Components\DI\ContainerAwareInterface;
use \Symfony\Component\Console\Helper\TableHelper;
use \Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * @class Command
 * @package Selene\Components\Console
 * @version $Id$
 */
class Command extends SymfonyCommand
{
    /**
     * getApp
     *
     *
     * @access public
     * @return mixed
     */
    public function getApp()
    {
        return $this->getApplication()->getApplication();
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
}
