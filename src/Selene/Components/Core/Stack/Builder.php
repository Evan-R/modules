<?php

/**
 * This File is part of the Selene\Components\Core\Stack package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Core\Stack;

use \Selene\Components\Core\AppCoreInterface;

/**
 * @class Builder
 * @package Selene\Components\Core\Stack
 * @version $Id$
 */
class Builder
{
    public function __construct()
    {
        $this->stack = new \SplStack;
    }

    public function push()
    {
        $this->stack->push($args = func_get_args());
    }

    /**
     * build
     *
     * @param AppCoreInterface $app
     *
     * @access public
     * @return mixed
     */
    public function resolve(AppCoreInterface $app)
    {
        $params = [$app];

        foreach ($this->stack as $definition) {

            $middleware = array_shift($definition);

            if ($middleware instanceof AppCoreInterface) {
                $app = $middleware;
            } elseif (is_string($middleware) && class_exists($middleware)) {
                array_unshift($definition, $app);
                $app = $this->getCoreClassInstance($middleware, $definition);
            } else {
                throw new \InvalidArgumentException('invalid middleware');
            }

            array_unshift($params, $app);
        }

        return new StackedCore($app, $params);
    }

    /**
     * getCoreClassInstance
     *
     * @param mixed $class
     * @param array $args
     *
     * @access private
     * @return mixed
     */
    private function getCoreClassInstance($class, array $args = [])
    {
        $reflection = new \ReflectionClass($class);

        if (!$reflection->implementsInterface($interface = 'Selene\Components\Core\AppCoreInterface')) {
            throw new \InvalidArgumentException(
                sprintf('Middleware must implement %s.', $interface)
            );
        }

        return $reflection->newInstanceArgs($args);
    }
}
