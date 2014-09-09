<?php

/*
 * This File is part of the Selene\Module\Routing\Filter package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Filter;

use \Selene\Module\Events\Event;
use \Selene\Module\Events\Dispatcher;
use \Selene\Module\Common\Traits\Getter;
use \Selene\Module\Events\DispatcherInterface;

/**
 * @class FilterLoader
 *
 * @package Selene\Module\Routing\Filter
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FilterLoader
{
    use Getter;

    private $events;
    private $filters;

    /**
     * Constructor.
     *
     * @param array $filters
     * @param DispatcherInterface $events
     */
    public function __construct(array $filters = [], DispatcherInterface $events = null)
    {
        $this->set($filters);
        $this->events = $events ?: new Dispatcher;
    }

    /**
     * add
     *
     * @param FilterInterface $filter
     *
     * @return void
     */
    public function add(FilterInterface $filter)
    {
        $this->filters[$filter->getType()][$filter->getName()][spl_object_hash($filter)] = &$filter;
    }

    /**
     * remove
     *
     * @param FilterInterface $filter
     *
     * @return void
     */
    public function remove(FilterInterface $filter)
    {
        unset($this->filters[$filter->getType()][$filter->getName()][spl_object_hash($filter)]);
    }

    /**
     * run
     *
     * @param string $filterName
     * @param int $type
     * @param Event $event
     *
     * @return void
     */
    public function run($filterName, $type, Event $event)
    {
        if (!isset($this->filters[$type][$filterName])) {
            return;
        }

        $res = null;

        foreach ($this->filters[$type][$filterName] as &$filter) {

            if (null !== ($res = $filter->run($event))) {
                $event->stopPropagation();

                return $res;
            }

            if ($event->isPropagationStopped()) {
                break;
            }
        }
    }

    /**
     * set
     *
     * @param array $filters
     *
     * @return void
     */
    public function set(array $filters)
    {
        $this->filters = [];

        foreach ($filters as &$filter) {
            $this->add($filter);
        }
    }

    /**
     * get
     *
     * @param int $type
     *
     * @return FilterInterface
     */
    public function get($type = FilterInterface::T_BEFORE)
    {
        return $this->getDefault($this->filters, $type, []);
    }
}
