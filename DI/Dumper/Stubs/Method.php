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

class Method extends Stub
{
    /**
     * method
     *
     * @var mixed
     */
    protected $method;

    /**
     * visibility
     *
     * @var mixed
     */
    protected $visibility;

    /**
     * type
     *
     * @var mixed
     */
    protected $type;


    public function __construct($methodName, array $arguments, MethodBody $body, $visibility = 'public', $type = null)
    {
        $this->method = $methodName;
        $this->arguments = $arguments;
        $this->type = $type;
        $this->visibility = $visibility;
        $this->body = $body;
    }

    /**
     * dump
     *
     * @access public
     * @return string
     */
    public function dump()
    {
        $body = $this->getBody();
        $type = $this->getType();
        $method = $this->method();
        $arguments = $this->getArgs();
        $visibility = $this->getVisibility();

        return <<<EOL
        $visibility function$type$method($arguments)
        {
            $body;
        }
EOL;
    }

    /**
     * getType
     *
     * @access protected
     * @return string
     */
    protected function getType()
    {
        return 'static' === $this->type ? ' static ' : '';
    }

    /**
     * getArgs
     *
     * @access public
     * @return string
     */
    public function getArgs()
    {
        return implode(', ', $this->arguments);
    }

    /**
     * getMethod
     *
     * @access protected
     * @return string
     */
    protected function getMethod()
    {
        return $this->method;
    }

    /**
     * getVisibility
     *
     * @access protected
     * @return string
     */
    protected function getVisibility()
    {
        return in_array($this->visibility, ['public', 'protected', 'private']) ? $this->visibility : 'public';
    }

    /**
     * getBody
     *
     * @access protected
     * @return string
     */
    protected function getBody()
    {
        return $this->body->dump();
    }
}
