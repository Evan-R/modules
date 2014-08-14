<?php

/**
 * This File is part of the Selene\Module\Config package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Config\Resource;

/**
 * @class FileResource extends AbstractResource
 * @see AbstractResource
 *
 * @package Selene\Module\Config
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class FileResource extends AbstractResource
{
    /**
     * {@inheritdoc}
     */
    public function exists()
    {
        return is_file($file = $this->getPath()) && stream_is_local($file);
    }
}
