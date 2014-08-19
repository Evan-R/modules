<?php

/*
 * This File is part of the Selene\Module\Writer\Generator\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Object;

use \Selene\Module\Writer\GeneratorInterface;

/**
 * @interface MethodInterface
 * @package Selene\Module\Writer\Generator\Object
 * @version $Id$
 */
interface MethodInterface extends GeneratorInterface
{
    public function setType($type);

    public function getName();

    public function setArguments(array $arguments);

    public function addArgument(Argument $argument);
}
