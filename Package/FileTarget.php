<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

/**
 * @class FileTarget
 * @package Selene\Components\Package
 * @version $Id$
 */
class FileTarget implements FileTargetInterface
{
    public function __construct($file, $relativePath = null)
    {
        $this->file = $file;
        $this->relativePath = $relativePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getSource()
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return basename($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        return file_get_contents($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        return file_exists($this->file);
    }
}
