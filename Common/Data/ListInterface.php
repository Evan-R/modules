<?php

/**
 * This File is part of the Selene\Module\Common\Data package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Common\Data;

interface ListInterface
{
    /**
     * push
     *
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function append($value);

    /**
     * insert
     *
     * @param int $index
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function insert($index, $value);

    /**
     * pop
     *
     * @param int $index
     *
     * @access public
     * @return mixed
     */
    public function pop($index = null);

    /**
     * remove
     *
     * @param mixed $value
     *
     * @access public
     * @return void
     */
    public function remove($value);

    /**
     * count
     *
     * @param mixed $value
     *
     * @access public
     * @return int
     */
    public function countValue($value);

    /**
     * sort
     *
     * @access public
     * @return mixed
     */
    public function sort();

    /**
     * reverse
     *
     * @access public
     * @return mixed
     */
    public function reverse();

    /**
     * extend
     *
     * @param ListStruct $list
     *
     * @access public
     * @return mixed
     */
    public function extend(ListInterface $list);
}
