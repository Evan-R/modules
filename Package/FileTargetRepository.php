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

use \Selene\Module\Filesystem\Filesystem;
use \Selene\Module\Filesystem\Traits\PathHelperTrait;
use \Selene\Module\Package\Traits\FileBackUpHelper;
use \Selene\Module\Package\Exception\MissingFileException;

/**
 * @class FileTargetRepository
 * @packageSelene\Module\Package
 * @version $Id$
 */
class FileTargetRepository implements FileRepositoryInterface
{
    use PathHelperTrait;
    use FileBackUpHelper;

    /**
     * files
     *
     * @var array
     */
    private $files;

    /**
     * Create a new FileTargetRepository instance.
     *
     * @param array $files
     * @param Filesystem $fs
     */
    public function __construct(array $files = [], Filesystem $fs = null)
    {
        $this->setFiles($files);
        $this->fs = $fs ?: new Filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function setFiles(array $files)
    {
        foreach ($files as $target) {
            $this->addFile($target);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function createTarget($file, $relPath = null)
    {
        $this->addFile(new FileTarget($file, $relPath));
    }

    /**
     * {@inheritdoc}
     */
    public function addFile(FileTargetInterface $target)
    {
        $this->files[] = $target;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpFiles($targetPath, $override = false)
    {
        foreach ($this->files as $file) {
            $this->dumpFile($file, $targetPath, $override);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException if source is not valid.
     *
     * @return string
     */
    public function dumpFile(FileTargetInterface $file, $targetPath, $override = false)
    {
        if (!$file->isValid()) {
            throw new MissingFileException(sprintf('source file "%s" does not exist.', $file->getSource()));
        }

        $target = $this->getTargetPath($file, $targetPath);

        if ($this->backupIfOverride($this->fs, $target, $override)) {
            $this->fs->setContents($target, $file->getContents());

            return $target;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetPath(FileTargetInterface $file, $targetPath)
    {
        $path = (null !== $file->getRelativePath()) ?
            $this->expandPath(rtrim($targetPath, '\\\/').DIRECTORY_SEPARATOR.trim($file->getRelativePath(), '\\\/')) :
            $targetPath;

        return $path . DIRECTORY_SEPARATOR . $file->getFilename();
    }
}
