<?php

/**
 * This File is part of the Selene\Module\View\Traits package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Traits;

use \Selene\Module\View\ManagerInterface;

/**
 * @trait ViewAwareTrait
 *
 * @package Selene\Module\View\Traits
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
trait ViewAwareTrait
{
    /**
     * view
     *
     * @var \Selene\Module\View\ManagerInterface
     */
    protected $view;

    /**
     * setView
     *
     * @param \Selene\Module\ViewManagerInterface $view
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
     * @return \Selene\Module\View\EnvironmentInterface
     */
    public function getView()
    {
        return $this->view;
    }
}
