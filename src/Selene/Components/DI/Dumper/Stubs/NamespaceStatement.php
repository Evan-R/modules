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
 * @class NamespaceStatement
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class NamespaceStatement extends Stub
{
    /**
     * @param mixed $namspace
     *
     * @access public
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $namespace = $this->namespace;
        return <<<EOL
namespace $namespace;
EOL;
    }
}
