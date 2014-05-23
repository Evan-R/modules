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
 * @class Constructor
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class Constructor extends Stub
{
    public function __construct()
    {
    }

    public function dump()
    {
        return $this->dumpConstructor();
    }

    private function dumpConstructor()
    {
        return <<<EOL
    /**
     * Create a new container instance
     */
    public function __construct()
    {
        parent::__construct(new StaticParameters(\$this->getDefaultParams()));

        \$this->aliases = [];
        \$this->internals = [];

        \$this->cmap = \$this->getContructorsMap();
        \$this->icmap = \$this->getInternalContructorsMap();
        \$this->locked = true;
    }

EOL;

    }
}
