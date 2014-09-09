<?php

/*
 * This File is part of the Selene\Module\Common\Data package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Data;

/**
 * @see https://mwop.net/blog/253-Taming-SplPriorityQueue.html
 *
 * @package Selene\Module\Common\Data
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class PriorityQueue extends \SplPriorityQueue
{
    private $queueOrder;

    public function __construct()
    {
        $this->queueOrder = PHP_INT_MAX;
    }

    public function insert($datum, $priority)
    {
        if (is_int($priority)) {
            $priority = [$priority, $this->queueOrder--];
        }

        parent::insert($datum, $priority);
    }
}
