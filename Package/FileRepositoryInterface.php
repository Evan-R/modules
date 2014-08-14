<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Package;

use \Selene\Module\Filesystem\Traits\PathHelperTrait;

/**
 * @class FileTargetRepository
 * @packageSelene\Module\Package
 * @version $Id$
 */
interface FileRepositoryInterface
{
    /**
     * @var boolean
     */
    const FILE_OVERRIDE = true;

    /**
     * @var boolean
     */
    const FILE_NO_OVERRIDE = false;

    /**
     * setFiles
     *
     * @param array $files
     *
     * @return void
     */
    public function setFiles(array $files);

    /**
     * createTarget
     *
     * @param string $file
     * @param string $relPath
     *
     * @return void
     */
    public function createTarget($file, $relPath = null);

    /**
     * addFile
     *
     * @param FileTargetInterface $target
     *
     * @return void
     */
    public function addFile(FileTargetInterface $target);

    /**
     * getFiles
     *
     * @return array
     */
    public function getFiles();

    /**
     * dumpFiles
     *
     * @param string  $targetPath
     * @param boolean $override
     *
     * @return void
     */
    public function dumpFiles($targetPath, $override = self::FILE_NO_OVERRIDE);

    /**
     * dumpFile
     *
     * @param FileTargetInterface $file
     * @param string $targetPath
     * @param boolean $override
     *
     * @return void
     */
    public function dumpFile(FileTargetInterface $file, $targetPath, $override = self::FILE_NO_OVERRIDE);

    /**
     * getTargetPath
     *
     * @param FileTargetInterface $file
     * @param string $targetPath
     *
     * @return string
     */
    public function getTargetPath(FileTargetInterface $file, $targetPath);
}
