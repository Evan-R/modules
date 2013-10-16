<?php

/**
 * This File is part of the Selene\Components\Filesystem package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Filesystem;

use Selene\Components\Common\Interfaces\Arrayable;
use Selene\Components\Common\Interfaces\ArrayableInterface;

/**
 * @class SplFileInfo
 * @see \SplFileInfo
 *
 * @package Selene\Components\Filesystem
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com
 * @license MIT
 */
class SplFileInfo extends \SplFileInfo implements ArrayableInterface
{
    private $relativePath;

    private $relativePathName;

    public function __construct($file, $relativePath = null, $relativePathName = null)
    {
        parent::__construct($file);
        $this->relativePath = $relativePath;
        $this->relativePathName = $relativePathName;
    }

    /**
     * toArray
     *
     * @access public
     * @return array
     */
    public function toArray()
    {
        $attributes = [
            'name'             => $this->getBasename(),
            'path'             => $this->getRealPath(),
            'relativePath'     => $this->getRelativePath(),
            'relativePathName' => $this->getRelativePathName(),
            'lastmod'          => $this->getMTime(),
            'type'             => $this->getType(),
            'owner'            => $this->getOwner(),
            'group'            => $this->getGroup(),
            'size'             => $this->getSize()
        ];

        if ($this->isFile()) {
            $attributes['extension'] = $this->getExtension();
            $attributes['mimetype']  = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $this->getRealPath());
        }
        return $attributes;
    }

    /**
     * getRelativePath
     *
     * @access public
     * @return string
     */
    public function getRelativePath()
    {
        return $this->relativePath;
    }

    /**
     * getRelativePathName
     *
     * @access public
     * @return string
     */
    public function getRelativePathName()
    {
        return $this->relativePathName;
    }
}
