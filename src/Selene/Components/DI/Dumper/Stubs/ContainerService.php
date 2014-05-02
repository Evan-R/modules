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

use \Selene\Components\DI\BaseContainer;
use \Selene\Components\DI\ContainerInterface;

/**
 * @class ContainerConstructor
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class ContainerService extends ServiceMethod
{

    public function __construct(ContainerInterface $container, $serviceId)
    {
        $this->setContainer($container);
        $this->serviceId = $serviceId;
    }

    protected function getMethodBody()
    {
        $id = $this->serviceId;
        return <<<EOL
return \$this->services['$id'] = \$this;
EOL;
    }
}
