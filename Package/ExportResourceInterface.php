<?php

/**
 * This File is part of the Selene\Module package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package;

/**
 * @interface ExportConfigInterface
 * @package Selene\Module
 * @version $Id$
 */
interface ExportResourceInterface
{
    /**
     * getExports
     *
     * @return void
     */
    public function getExports(FileRepositoryInterface $files);
}
