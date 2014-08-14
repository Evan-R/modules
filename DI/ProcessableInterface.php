<?php

/**
 * This File is part of the Selene\Module\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\DI;

use \Selene\Module\DI\Processor\ProcessorInterface;

/**
 * @interface ProcessableInterface
 *
 * @package Selene\Module\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
interface ProcessableInterface
{
    /**
     * process
     *
     * @param ProcessorInterface $processor
     *
     * @access public
     * @return void
     */
    public function process(ProcessorInterface $processor);
}
