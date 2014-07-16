<?php

/**
 * This File is part of the Selene\Components\Routing\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing\Events;

use \Selene\Components\Routing\Matchers\MatchContext;

/**
 * @class RouteDispatchEvent
 * @package Selene\Components\Routing\Events
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class RouteDispatchEvent extends RouteEvent
{
    /**
     * context
     *
     * @var \Selene\Components\Routing\Matchers\MatchContext
     */
    private $context;

    /**
     * response
     *
     * @var mixed
     */
    private $response;

    /**
     * Contructor.
     *
     * @param MatchContext $context
     */
    public function __construct(MatchContext $context)
    {
        $this->context = $context;

        parent::__construct($context->getRoute(), $context->getRequest());
    }

    /**
     * getContext
     *
     *
     * @return MatchContext
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set the response
     *
     * @param mixed $response
     *
     * @return void
     */
    public function setResponse($response)
    {
        if (!$this->isPropagationStopped()) {
            $this->response = $response;
        }
    }

    /**
     * Get the response
     *
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
