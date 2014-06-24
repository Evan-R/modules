<?php

/**
 * This File is part of the Selene\Components\View\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Traits;

use \Selene\Components\View\ManagerInterface;

/**
 * @trait ViewAwareTrait
 *
 * @package Selene\Components\View\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait ViewAwareTrait
{
    /**
     * view
     *
     * @var \Selene\Components\View\ManagerInterface
     */
    protected $view;

    /**
     * setView
     *
     * @param \Selene\Components\ViewManagerInterface $view
     *
     * @access public
     * @return mixed
     */
    public function setView(ManagerInterface $view)
    {
        $this->view = $view;
    }

    /**
     * getView
     *
     * @access public
     * @return \Selene\Components\View\EnvironmentInterface
     */
    public function getView()
    {
        return $this->view;
    }
}
