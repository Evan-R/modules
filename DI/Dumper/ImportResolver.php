<?php

/**
 * This File is part of the Selene\Components\DI\Dumper package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper;

/**
 * @class ImportResolver
 * @package Selene\Components\DI\Dumper
 * @version $Id$
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
        $import = $this->pad($import);

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
        $import = $this->pad($import);

        if (isset($this->aliases[$import])) {
            return $this->aliases[$import];
        }

        if (isset($this->imports[$import])) {
            return $this->imports[$import];
        }

        return $import;
    }

    /**
     * getImport
     *
     * @param mixed $import
     *
     * @return string
     */
    public function getImport($import)
    {
        $import = $this->pad($import);

        if (isset($this->aliases[$import])) {
            return $import.' as '.$this->aliases[$import];
        }

        return $import;
    }

    /**
     * setAlias
     *
     * @param string $import
     * @param string $name
     *
     * @return void
     */
    protected function setAlias($import, $name)
    {
        $alias = $name.'Alias';

        if (!in_array($name, $this->names)) {
            $this->names[] = $name;
        }

        // if there is no import
        if (isset($this->imports[$import])) {
            return;
        }

        $aliases = array_values($this->aliases);

        while (in_array($alias, $this->aliases)) {
            $alias .= 'Alias';
        }

        $this->aliases[$import] = $alias;
    }

    protected function getBaseName($import)
    {
        if (1 === substr_count($import, '\\')) {
            return ltrim($import, '\\');
        }

        return substr($import, strrpos($import, '\\') + 1);
    }

    private function pad($import)
    {
        return '\\'.ltrim($import, '\\');
    }
}
