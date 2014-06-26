<?php

/**
 * This File is part of the Selene\Components package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

/**
 * @interface ExportConfigInterface
 * @package Selene\Components
 * @version $Id$
 */
interface ExportConfigInterface
{
    /**
     * getExports
     *
     * @return array
     */
    public function getExports(FileRepositoryInterface $files);
}
