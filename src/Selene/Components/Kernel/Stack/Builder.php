<?php

/**
 * This File is part of the Selene\Components\Core\Stack package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Kernel\Stack;

use \Symfony\Component\HttpKernel\HttpKernelInterface;

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
    public function resolve(HttpKernelInterface $app)
    {
        $params = [$app];

        foreach ($this->stack as $definition) {

            $middleware = array_shift($definition);

            if ($middleware instanceof HttpKernelInterface) {
                $app = $middleware;
            } elseif (is_string($middleware) && class_exists($middleware)) {
                array_unshift($definition, $app);
                $app = $this->getCoreClassInstance($middleware, $definition);
            } else {
                throw new \InvalidArgumentException('invalid middleware');
            }

            array_unshift($params, $app);
        }

        return new KernelStack($app, $params);
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

        if (!$reflection->implementsInterface($interface = 'Symfony\Component\HttpKernel\HttpKernelInterface')) {
            throw new \InvalidArgumentException(
                sprintf('Middleware must implement %s.', $interface)
            );
        }

        return $reflection->newInstanceArgs($args);
    }
}
