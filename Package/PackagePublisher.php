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

use \Selene\Components\Filesystem\Filesystem;
use \Selene\Components\Package\Traits\FileBackUpHelper;
use \Selene\Components\Package\Dumper\ConfigDumperInterface;
use \Selene\Components\Package\Dumper\DelegateAbleDumperInterface;
use \Selene\Components\Package\PackageRepositoryInterface as Packages;
use \Selene\Components\Package\PackageInterface as IPackage;

/**
 * @class PackagePublisher
 * @package Selene\Components\Package
 * @version $Id$
 */
class PackagePublisher
{
    use FileBackUpHelper;

    const FORMAT_XML = 'xml';

    const FORMAT_PHP = 'php';

    const PUBLISH = 0;

    const NO_PUBLISH = 1;

    /**
     * exceptions
     *
     * @var array
     */
    private $exceptions;

    /**
     * targetPath
     *
     * @var string
     */
    private $targetPath;

    /**
     * dumper
     *
     * @var mixed
     */
    private $dumper;

    /**
     * format
     *
     * @var string
     */
    private $format;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @param Packages $packages
     * @param ConfigDumperInterface $dumper
     * @param string $path
     */
    public function __construct(Packages $packages = null, ConfigDumperInterface $dumper = null, $path = null)
    {
        $this->packages = $packages;
        $this->dumper   = $dumper;

        $this->targetPath = $path;
        $this->exceptions = [];
    }

    /**
     * hasErrors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return (bool)$this->exceptions;
    }

    /**
     * getErrors
     *
     * @param string $package
     *
     * @return array|null
     */
    public function getErrors($package = null)
    {
        return null !== $package ?
            (isset($this->exceptions[$package]) ? $this->exceptions[$package] : null) :
            $this->exceptions;
    }

    /**
     * setPackages
     *
     * @param PackageRepositoryInterface $packages
     *
     * @return void
     */
    public function setPackages(Packages $packages)
    {
        $this->packages = $packages;
    }

    /**
     * setTargetPath
     *
     * @param mixed $targetPath
     *
     * @return void
     */
    public function setTargetPath($targetPath)
    {
        $this->targetPath = $targetPath;
    }

    /**
     * setFileFormat
     *
     * @param mixed $format
     *
     * @return void
     */
    public function setFileFormat($format)
    {
        if (!$this->dumper || !$this->supports($format)) {
            throw new \InvalidArgumentException(sprintf('Format "%s" is unsupported', $format));
        }
    }

    /**
     * getDefaultFormat
     *
     * @return string
     */
    public function getFileFormat()
    {
        return $this->format ?: static::FORMAT_XML;
    }

    /**
     * setFilesystem
     *
     * @param Filesystem $fs
     *
     * @return void
     */
    public function setFilesystem(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * publish
     *
     * @param string $name
     * @param string $targetPath
     * @param boolean $override
     * @param boolean $force
     *
     * @return void
     */
    public function publish($name = null, $targetPath = null, $override = false, $force = false)
    {
        if (!$this->packages) {
            throw new \BadMethodCallException('no packages found');
        }

        if (null === $name) {
            return $this->publishAll($targetPath, $override, $force);
        }

        if (!$this->packages->has($name)) {
            throw new \InvalidArgumentException(
                sprintf('A package with name "%s" does not exist.', $name)
            );
        }

        $package = $this->packages->get($name);

        return $this->publishPackage($package, $targetPath, $override, $force);
    }

    /**
     * publishAll
     *
     * @param string $targetPath
     * @param boolean $override
     * @param boolean $force
     *
     * @return void
     */
    public function publishAll($targetPath = null, $override = false, $force = false)
    {
        foreach ($this->packages as $package) {
            $this->publishPackage($package, $targetPath, $override, $force);
        }
    }

    /**
     * publisPackage
     *
     * @param PackageInterface $package
     * @param string  $target
     * @param boolean $override
     * @param boolean $force
     *
     * @return integer
     */
    public function publishPackage(IPackage $package, $target = null, $override = false, $force = false)
    {
        $name = strtolower($package->getAlias());

        if (!$package instanceof ExportConfigInterface) {
            return $this->publishDefault($name, $target, $override, $force);
        }

        $package->getExports($files = new FileTargetRepository([], $this->getFilesystem()));

        try {
            $files->dumpFiles($this->getPackagePath($name, $target ?: $this->targetPath), $override);

            return static::PUBLISH;

        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('[%s]: %s', $name, $e->getMessage()));
        }
    }

    /**
     * publishDefault
     *
     * @param string  $name
     * @param string  $target
     * @param boolean $override
     * @param boolean $force
     *
     * @return void
     */
    protected function publishDefault($name, $target = null, $override = false, $force = false)
    {
        if (!$force) {
            return static::NO_PUBLISH;
        }

        $fs = $this->getFilesystem();
        $file = $this->getFile($name, $format = $this->getFileFormat());

        if ($this->backupIfOverride($fs, $file, $override)) {
            $fs->setContents($file, $this->dumper->dump($name, [], $format));
        }

        return static::PUBLISH;
    }

    /**
     * getFile
     *
     * @param mixed $name
     * @param mixed $format
     * @param mixed $targetPath
     *
     * @access protected
     * @return string
     */
    protected function getFile($name, $format = null, $targetPath = null)
    {
        if (!$path = $this->getPackagePath($name, $targetPath ?: $this->targetPath)) {
            return;
        }

        $fileName = $this->dumper instanceof DelegateAbleDumperInterface ?
            $this->dumper->getDumper($format)->getFilename() :
            $this->dumper->getFilename();

        return $path . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * getPackagePath
     *
     * @param PackageInterface $package
     *
     * @return string
     */
    protected function getPackagePath($name, $path)
    {
        if (!is_dir($target = $path . DIRECTORY_SEPARATOR . $name)) {
            if (false === @mkdir($target, 0755, true)) {
                return;
            }
        }

        return $target;
    }

    /**
     * getFilesystem
     *
     * @access private
     * @return Filesystem
     */
    private function getFilesystem()
    {
        if (null === $this->fs) {
            $this->fs = new Filesystem;
        }

        return $this->fs;
    }
}
