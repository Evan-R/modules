<?php

/**
 * This File is part of the Selene\Components\Config\Validation\Nodes package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Validation\Nodes;

interface NodeInterface
{
    public function required();

    /**
     * optional
     *
     * @access public
     * @return Node
     */
    public function optional();

    /**
     * isOptional
     *
     * @access public
     * @return mixed
     */
    public function isOptional();

    /**
     * isRequired
     *
     * @access public
     * @return mixed
     */
    public function isRequired();

    /**
     * end
     *
     * @access public
     * @return mixed
     */
    public function end();
}
