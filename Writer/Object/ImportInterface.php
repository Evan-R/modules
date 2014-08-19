<?php

/*
 * This File is part of the Selene\Module\Writer\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Object;

/**
 * @class ImportInterface
 * @package Selene\Module\Writer\Object
 * @version $Id$
 */
interface ImportInterface
{
    public function setResolver(ImportResolver $resolver);
}
