<?php

/**
 * This File is part of the Selene\Module\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Events;

use \Selene\Module\Events\Event;
use \Selene\Module\Routing\Route;
use \Symfony\Component\HttpFoundation\Request;

/**
 * @class RouteFilterAfterEvent
 * @package Selene\Module\Routing\Events
 * @version $Id$
 */
class RouteFilterAfterEvent extends RouteFilterEvent
{
    private $response;

    public function __construct(RouteDispatchEvent $dispatched)
    {
        parent::__construct($dispatched->getRoute(), $dispatched->getRequest());
        $this->dispatched = $dispatched;
    }

    public function setResponse($response)
    {
        $this->dispatched->setResponse($response);
    }

    /**
     * getResponse
     *
     * @access public
     * @return mixed
     */
    public function getResponse()
    {
        $this->dispatched->getResponse();
    }
}
