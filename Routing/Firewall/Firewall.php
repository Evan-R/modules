<?php

/*
 * This File is part of the Selene\Module\Routing\Firewall package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Firewall;

use \Selene\Module\Routing\Filter\FilterInterface;

/**
 * @class Firewall
 *
 * @package Selene\Module\Routing\Firewall
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Firewall implements SubscriberInterface
{
    /**
     * Constructor.
     *
     * @param array $filters
     *
     * @return void
     */
    public function __construct(array $filters = [])
    {
        $this->setFilters($filters);
    }

    /**
     * onMatch
     *
     * @param MatchContext $context
     * @param array $filters
     *
     * @return void
     */
    public function onMatched(RouteMatched $event)
    {
        if (Request::SUB_REQUEST === $event->getRequestType()) {
            return;
        }

        foreach ($event->getRoute()->getBeforeFilters() as $filterName) {

            if (!isset($this->filters[$filterName])) {
                continue;
            }

            $this->filters[$filterName]->run($context);

            if ($response = $this->context->getRequest()->getResponse()) {
                throw FilterException::blockedRequest($response);
            }
        }
    }

    /**
     * setFilters
     *
     * @param array $filters
     *
     * @return void
     */
    public function setFilters(array $filters)
    {
        $this->filter = $filters;

        foreach ($filters as $name => $filter) {
            $this->addFilter($name, $filter);
        }
    }

    /**
     * addFilter
     *
     * @param string $name
     * @param FilterInterface $filter
     *
     * @return void
     */
    public function addFilter($name, FilterInterface $filter)
    {
        $this->filters[$name] = $filter;
    }

    public function getSubscriptions()
    {
        return [
            RouterEvents::MATCHED => 'onMatched'
        ];
    }
}
