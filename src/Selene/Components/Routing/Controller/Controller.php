<?php

/**
 * This File is part of the Selene\Components\Routing\Controller package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Controller;

use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;

/**
 * @class AbstractController implements ContainerAwareInterface
 * @see ContainerAwareInterface
 *
 * @package Selene\Components\Routing\Controller
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
abstract class Controller extends BaseController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * getView
     *
     * @access public
     * @return mixed
     */
    public function getView()
    {
        return $this->getContainer()->get('view');
    }

    /**
     * getRequest
     *
     * @access public
     * @return mixed
     */
    public function getRequest()
    {
        return $this->getContainer()->get('request.stack')->getCurrent();
    }

    /**
     * render
     *
     * @param mixed $view
     *
     * @access public
     * @return mixed
     */
    protected function render($view)
    {
        return $this->getContainer()->get('view')->render($view);
    }
}
