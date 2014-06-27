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

use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\ContainerAwareInterface;
use \Selene\Components\DI\Traits\ContainerAwareTrait;
use \Selene\Components\DI\Dumper\Stubs\ServiceMethod;
use \Selene\Components\DI\Dumper\Stubs\ContainerService;
use \Selene\Components\DI\Dumper\Stubs\Constructor;
use \Selene\Components\DI\Dumper\Stubs\ClassHeader;
use \Selene\Components\DI\Dumper\Stubs\ClassFooter;
use \Selene\Components\DI\Dumper\Stubs\Property;
use \Selene\Components\DI\Dumper\Stubs\String;
use \Selene\Components\DI\Dumper\Stubs\UseStatements;
use \Selene\Components\DI\Dumper\Stubs\NamespaceStatement;
use \Selene\Components\DI\Dumper\Traits\FormatterTrait;

/**
 * @class Dumper
 * @package Selene\Components\DI
 * @version $Id$
 */
class PhpDumper implements ContainerAwareInterface
{
    use FormatterTrait, ContainerAwareTrait;

    /**
     * @param ContainerInterface $container
     * @param string $namespace
     * @param string $className
     * @param string $containerServiceName
     *
     * @access public
     */
    public function __construct(ContainerInterface $container, $namespace, $className, $containerServiceName)
    {
        $this->namespace = $namespace;
        $this->className = $className;
        $this->containerServiceName = $containerServiceName;
        $this->contents = [];
        $this->setContainer($container);
    }

    /**
     * @return string
     */
    public function dump()
    {
        $this->process();

        return $this->dumpContents();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->dump();
    }

    /**
     * getClassPropertyValues
     *
     * @return array
     */
    protected function getClassPropertyValues()
    {
        return [
            ['cmap', 'protected', null, 'array'],
            ['icmap', 'protected', null, 'array'],
            ['internals', 'protected', null, 'array'],
            ['locked', 'protected', null, 'boolean'],
        ];
    }

    /**
     * startProcess
     *
     * @access protected
     * @return void
     */
    protected function process()
    {
        $header = new ClassHeader(
            $this->className,
            '\\'.get_class($this->container),
            new NamespaceStatement($this->namespace),
            new UseStatements
        );

        $this->add($header);

        // add properties
        foreach ($this->getClassPropertyValues() as $prop) {
            $this->add(new Property($prop[0], $prop[1], $prop[2], $prop[3]));
        }

        // add constructor
        $this->add(new Constructor);

        foreach ($this->getContainer()->getDefinitions() as $id => $definition) {

            if ($definition->isAbstract()) {
                continue;
            }

            if ($this->containerServiceName === $id) {
                $definition->setClass($this->getContainerClassName());
                $this->add(new ContainerService($this->getContainer(), $id));
                continue;
            }

            $this->add(new ServiceMethod($this->getContainer(), $id));
        }

        $this->add(new String($this->getDefaultClassMethods()));
        $this->add(new String($this->mapConstructorNames()));
        $this->add(new String($this->getDefaultParameters()));

        $this->add(new ClassFooter);
    }

    /**
     * getContainerClassName
     *
     * @access protected
     * @return string
     */
    protected function getContainerClassName()
    {
        return $this->namespace . '\\' .$this->className;
    }

    /**
     * writeContents
     *
     *
     * @access protected
     * @return void
     */
    protected function writeContents()
    {
        file_put_contents($this->target, $this->dumpContents());
    }

    /**
     * dumpContents
     *
     * @access protected
     * @return mixed
     */
    protected function dumpContents()
    {
        return implode("\n", $this->content);
    }

    /**
     * add
     *
     * @param mixed $string
     *
     * @access protected
     * @return mixed
     */
    protected function add($string)
    {
        $this->content[] = $string;
    }

    /**
     * getDefaultParameters
     *
     * @access protected
     * @return string
     */
    protected function getDefaultParameters()
    {
        $params = $this->extractParams($this->container->getParameters()->all());

        return <<<EOL

    /**
     * Return the default parameter array;
     * @return array
     */
    private function getDefaultParams()
    {
        return $params;
    }
EOL;
    }

    protected function mapConstructorNames()
    {
        $names = [];
        $internal = [];

        foreach ($this->container->getDefinitions() as $id => $definition) {
            if ($definition->isInternal()) {
                $internal[$id] = ServiceMethod::getServiceGetterName($id, $definition->isInjected(), true);
            } else {
                $names[$id] = ServiceMethod::getServiceGetterName($id, $definition->isInjected(), false);
            }
        }

        $map = $this->extractParams($names);
        $imap = $this->extractParams($internal);
        return <<<EOL

    /**
     * get service names and methods
     */
    protected function getContructorsMap()
    {
        return $map;
    }

    /**
     * get service names and methods for internal services
     */
    protected function getInternalContructorsMap()
    {
        return $imap;
    }
EOL;
    }

    protected function getDefaultClassMethods()
    {
        return <<<EOL

    /**
     * {@inheritdoc}
     */
    public function getAlias(\$id)
    {
        return \$this->getDefault(\$this->aliases, \$id, \$id);
    }

    /**
     * {@inheritdoc}
     */
    public function get(\$id)
    {
        if (isset(\$this->services[\$id = \$this->getAlias(\$id)])) {
            return \$this->services[\$id];
        }

        if (isset(\$this->cmap[\$id])) {
            return call_user_func([\$this, \$this->cmap[\$id]], \$id);
        }

        return parent::get(\$id);
    }

    /**
     * Get an internal service
     */
    protected function getInternal(\$id)
    {
        if (isset(\$this->internals[\$id = \$this->getAlias(\$id)])) {
            return \$this->internals[\$id];
        }

        if (isset(\$this->icmap[\$id])) {
            return call_user_func([\$this, \$this->icmap[\$id]], \$id);
        }
    }

    /**
     * resolveId
     *
     * @param string \$id
     *
     * @access protected
     * @return string
     */
    protected function resolveId(\$id)
    {
        return \$this->getDefault(\$this->aliases, \$id, \$id);
    }

    /**
     * {@inheritdoc}
     */
    public function has(\$id)
    {
        return array_key_exists(\$id, \$this->cmap);
    }

    /**
     * Post check synced callers
     *
     * Post check if a setter of a called service that
     * has synced dependencies needs to be called immediately.
     */
    private function checkSynced(array &\$synced)
    {
        foreach (\$synced as \$id => \$sync) {
            if (isset(\$this->services[\$id])) {
                unset(\$synced[\$id]);
            }
        }
    }
EOL;
    }
}
