<?php

/**
 * This File is part of the Selene\Module\Config\Validator package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Validator;

/**
 * @interface TreeValidatorInterface
 * @package Selene\Module\Config\Validator
 * @version $Id$
 */
interface TreeValidatorInterface
{
    /**
     * getRoot
     *
     * @access public
     * @return DictNode
     */
    public function getRoot();

    /**
     * load
     *
     * @param array $config
     *
     * @access public
     * @return mixed
     */
    public function load(array $config);

    /**
     * validate
     *
     * @param array $values
     *
     * @access public
     * @return array
     */
    public function validate();
}
