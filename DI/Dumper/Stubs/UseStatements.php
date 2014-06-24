<?php

/**
 * This File is part of the Selene\Components\DI\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Stubs;

/**
 * @class UseStatements
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class UseStatements extends Stub
{
    /**
     * @param array $classes
     *
     * @access public
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $classes = $this->getClasses();
        return 'use '.implode(";\nuse ", $classes).";\n";
    }

    /**
     * getDefaultClasses
     *
     * @access protected
     * @return array
     */
    protected function getDefaultClasses()
    {
        return [
            '\Selene\Components\DI\BaseContainer',
            '\Selene\Components\DI\ContainerInterface',
            '\Selene\Components\DI\ParameterInterface',
            '\Selene\Components\DI\StaticParameters',
            '\Selene\Components\Common\Traits\Getter',
        ];
    }

    private function getClasses()
    {
        return array_unique(array_merge($this->classes, $this->getDefaultClasses()));
    }
}
