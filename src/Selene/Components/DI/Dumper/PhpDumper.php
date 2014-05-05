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
use \Selene\Components\DI\Dumper\Stubs\UseStatements;
use \Selene\Components\DI\Dumper\Stubs\NamespaceStatement;
use \Selene\Components\DI\Dumper\Traits\FormatterTrait;

/**
 * @class Dumper
 * @package Selene\Components\DI\Dumper
 * @version $Id$
 */
class PhpDumper implements ContainerAwareInterface
{
    use FormatterTrait, ContainerAwareTrait;

    /**
     * @param ContainerInterface $container
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
     * @access public
     * @return string
     */
    public function dump()
    {
        $this->process();

        //echo '<pre>';
        //echo $this->dumpContents();
        //echo '</pre>';
        return $this->dumpContents();
    }

    /**
     * @access public
     * @return string
     */
    public function __toString()
    {
        return $this->dump();
    }

    /**
     * startProcess
     *
     * @access protected
     * @return mixed
     */
    protected function process()
    {
        $header = new ClassHeader($this->className, '\\'.get_class($this->container), new NamespaceStatement($this->namespace), new UseStatements);

        $this->add($header);

        $this->add(new Property('cmap', 'protected', null, 'array'));
        $this->add(new Property('locked', 'protected', null, 'boolean'));

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

        $this->add($this->mapConstructorNames());

        $this->add($this->getDefaultClassMethods());

        $this->add(new ClassFooter);
    }

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

    protected function mapConstructorNames()
    {
        $names = [];

        foreach ($this->container->getDefinitions() as $id => $definition) {
            if ($definition->isInjected()) {
                $names[$id] = ServiceMethod::getServiceGetterName($id, true);
            } else {
                $names[$id] = ServiceMethod::getServiceGetterName($id);
            }
        }

        $map = $this->extractParams($names);
        return <<<EOL

    /**
     * get service names and methods
     */
    protected function getContructorsMap()
    {
        return $map;
    }
EOL;
    }

    protected function getDefaultClassMethods()
    {
        $parameters = $this->extractParams($this->container->getParameters()->all());

        return <<<EOL

    /**
     * {@iniheritdoc}
     */
    public function getAlias(\$id)
    {
        return \$this->getDefault(\$this->aliases, \$id, \$id);
    }

    /**
     * {@iniheritdoc}
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
     * {@iniheritdoc}
     */
    public function hasService(\$id)
    {
        return array_key_exists(\$id, \$this->cmap);
    }

    /**
     * {@iniheritdoc}
     */
    protected function getDefaultParameters()
    {
        return $parameters;
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
