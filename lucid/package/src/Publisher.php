<?php

/**
 * This File is part of the Selene\Module\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Package;

use UnexpectedValueException;
use LogicException;
use InvalidArgumentException;

/**
 * @class PackagePublisher
 * @package Selene\Module\Package
 * @version $Id$
 */
class Publisher
{
    use FileBackUpHelper;

    /** @var string */
    const FMT_XML    = 'xml';

    /** @var string */
    const FMT_PHP    = 'php';

    /** @var int */
    const PUBLISHED     = 0;

    /** @var int */
    const NOT_PUBLISHED = 1;

    /** @var array */
    private $exceptions;

    /** @var string */
    private $targetPath;

    /** @var DumperInterface */
    private $dumper;

    /** @var string */
    private $format;

    /**
     * Constructor.
     *
     * @param Packages $packages
     * @param ConfigDumperInterface $dumper
     * @param string $path
     */
    public function __construct(RepositoryInterface $providers = null, DumperInterface $dumper = null, $path = null)
    {
        $this->packages   = $providers;
        $this->dumper     = $dumper;
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
     * @param RepositoryInterface $packages
     *
     * @return void
     */
    public function setPackages(RepositoryInterface $packages)
    {
        $this->packages = $packages;
    }

    /**
     * setTargetPath
     *
     * @param string $targetPath
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
     * @param string $format
     *
     * @return void
     */
    public function setFileFormat($format)
    {
        if (!$this->dumper || !$this->dumper->supports($format)) {
            throw new UnexpectedValueException(sprintf('Format "%s" is unsupported.', $format));
        }

        $this->format = $format;
    }

    /**
     * getDefaultFormat
     *
     * @return string
     */
    public function getFileFormat()
    {
        return $this->format ?: self::FMT_XML;
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
            throw new LogicException('Cannot publish packages. No Packages set.');
        }

        if (null === $name) {
            return $this->publishAll($targetPath, $override, $force);
        }

        if (!$this->packages->has($name)) {
            throw new InvalidArgumentException(
                sprintf('A package with name "%s" does not exist.', $name)
            );
        }

        return $this->publishPackage($this->packages->get($name), $targetPath, $override, $force);
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
    public function publishPackage(ProviderInterface $package, $target = null, $override = false, $force = false)
    {
        if (!$package instanceof ExportResourceInterface) {
            return $this->publishDefault($package, $target, $override, $force);
        }

        return $this->publishFiles($package, $target, $override);
    }

    /**
     * publishFiles
     *
     * @param IPackage $package
     * @param string   $target
     * @param boolean  $override
     *
     * @return integer
     */
    protected function publishFiles(ProviderInterface $package, $target = null, $override = false)
    {
        $package->getExports($files = new FileTargetRepository([], $this->getFilesystem()));

        $path = $this->getPackagePath($package->getAlias(), $target ?: $this->targetPath);

        foreach ($files->getFiles() as $file) {
            $this->publishFile($file, $path, $override);
        }

        return self::PUBLISHED;
    }


    /**
     * publishFile
     *
     * @param mixed $file
     * @param mixed $path
     * @param mixed $override
     *
     * @return bool
     */
    protected function publishFile($file, $path, $override)
    {
        return $files->dumpFile($file, $path, $override);
    }

    /**
     * publishDefault
     *
     * @param string  $name
     * @param string  $target
     * @param boolean $override
     * @param boolean $force
     *
     * @return int
     */
    protected function publishDefault(ProviderInterface $package, $target = null, $override = false, $force = false)
    {
        if (!$force) {
            return self::NOT_PUBLISHED;
        }

        $file = $this->getFile($name = $package->getAlias(), $format = $this->getFileFormat());

        if ($this->backupIfOverride($file, $override)) {
            file_put_contents($file, $this->dumper->dump($name, [], $format), LOCK_EX);

            return self::PUBLISHED;
        }

        return self::NOT_PUBLISHED;
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
     * backupIfOverride
     *
     * @todo iwyg <mail@thomas-appel.com>; Di  5 Jan 14:02:42 2016 -->
     * Implement logic
     *
     * @param mixed $file
     * @param mixed $override
     *
     * @return bool
     */
    private function backupIfOverride($file, $override)
    {
        if (!is_file($file)) {
            // ensure parent directory exists.
        } elseif (false === $override) {
            return false;
        }

        //do backup stuff.
        return true;
    }
}
