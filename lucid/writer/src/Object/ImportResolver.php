<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

/**
 * This class handles class, interface, and trait aliases.
 *
 * @class ImportResolver
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ImportResolver
{
    /**
     * aliases
     *
     * @var array
     */
    private $aliases;

    /**
     * imports
     *
     * @var array
     */
    private $imports;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->aliases = [];
        $this->imports = [];
    }

    /**
     * add
     *
     * @param string $import
     *
     * @return void
     */
    public function add($import)
    {
        $import = $this->prepareImport($this->pad($import));

        $name = $this->getBaseName($import);

        $imports = array_keys($this->imports);

        if (in_array($name, $this->imports) && !in_array($import, $imports)) {
            $this->setAlias($import, $name);
        }

        if (!isset($this->imports[$import])) {
            $this->imports[$import] = $name;
        }
    }

    /**
     * getAlias
     *
     * @param string $import
     *
     * @return string
     */
    public function getAlias($import)
    {
        list($import, $alias) = $this->splitImport($o = $this->pad($import));

        if (isset($this->aliases[$import])) {
            return $this->aliases[$import];
        }

        if (isset($this->imports[$import])) {
            return $this->imports[$import];
        }

        return $o;
    }

    /**
     * Does the alias exitst?
     *
     * @param string $import
     *
     * @return boolean
     */
    public function hasAlias($import)
    {
        list($import, $alias) = $this->splitImport($o = $this->pad($import));

        return isset($this->aliases[$import]);
    }

    /**
     * Gets the import name as used with the use statement.
     *
     * If an alias exists for the given import, this method will return the
     * original import name suffixed with the alias, e.g. `\Foo as FooAlias`
     *
     * @param string $import
     *
     * @return string
     */
    public function getImport($import)
    {
        list($import, $alias) = $this->splitImport($o = $this->pad($import));

        if (isset($this->aliases[$import])) {
            return $import.' as '.$this->aliases[$import];
        }

        return $o;
    }

    /**
     * Generate an alias name for an import.
     *
     * @param string $import
     * @param string $name
     *
     * @return void
     */
    protected function setAlias($import, $name)
    {
        if (isset($this->aliases[$import])) {
            return;
        }

        $parts = preg_split('~\\\~', $import, -1, PREG_SPLIT_NO_EMPTY);
        array_pop($parts);
        $alias = $name;

        while (in_array($alias = $this->getAliasName($alias, $parts), $this->aliases)) {
            continue;
        }

        $this->aliases[$import] = $alias;
    }

    /**
     * Construct the alias name.
     *
     * @param string $alias
     * @param array  $parts
     * @param string $suffix
     *
     * @return string
     */
    protected function getAliasName($alias, array &$parts, $suffix = 'Alias')
    {
        if (0 < count($parts)) {
            $alias = array_pop($parts) . $alias;
        } else {
            $alias = $alias . $suffix;
        }

        return $alias;
    }

    /**
     * Get the import base name without the namespace.
     *
     * @param string $import
     *
     * @return string
     */
    protected function getBaseName($import)
    {
        if (1 === substr_count($import, '\\')) {
            return ltrim($import, '\\');
        }

        return substr($import, strrpos($import, '\\') + 1);
    }

    /**
     * prepareImport
     *
     * @param string $import
     *
     * @return string
     */
    protected function prepareImport($import)
    {
        list ($import, $alias) = $this->splitImport($import);

        if (null !== $alias && !in_array($alias, $this->aliases)) {
            $this->aliases[$import] = $alias;
        }

        return $import;
    }

    /**
     * splitImport
     *
     * @param string $import
     *
     * @return array
     */
    protected function splitImport($import)
    {
        if (1 === substr_count($import, $delim = ' as ')) {
            list ($import, $alias) = explode($delim, $import);

            return [$import, $alias];
        }

        return [$import, null];
    }


    /**
     * pad
     *
     * @param string $import
     *
     * @return string
     */
    private function pad($import)
    {
        return sprintf('\%s', ltrim($import, '\\'));
    }
}
