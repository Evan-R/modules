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

use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Definition;
use \Selene\Components\DI\ContainerInterface;
use \Jeremeamia\SuperClosure\SerializableClosure;

/**
 * @class ContainerDumper
 * @package Selene\Components\DI\Dumper
 * @version $Id$
 */
class ContainerDumper
{
    public function __construct()
    {
        $this->contents = [];
    }

    /**
     * setContainer
     *
     * @param ContainerInterface $container
     *
     * @access public
     * @return void
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function dump()
    {
        $this->add($this->open('CachedContainer', 'Container', 'App\Cached'));
        $this->getClassBody();
        $this->add($this->close());

        return implode("\n", $this->contents);
    }

    protected function getClassBody()
    {
        $this->add($this->getClassProperties());
        $this->add($this->getConstuctor());
        $this->add($this->getConstructorMethods());
        $this->add($this->getDefaultClassMethods());
    }

    protected function getConstructorMethods()
    {
        $names = [];
        $methods = [];

        foreach ($this->container->getServiceDefinitions() as $id => $service) {
            $indent = str_repeat(' ', 4);
            $head = <<<EOL

    /**
     * Service $id
     */\n
EOL;
            $body = $this->getMethodBody($service, $id);
            $method = $head.sprintf("%sprotected function %s(\$id)\n%s{%s%s}", $indent, $names[$id] = Container::camelCaseStr($id), $indent, $body, $indent);
            $methods[] = $method;
        }

        $methods[] = $this->mapConstructorNames($names);

        return implode("\n", $methods);
    }

    protected function mapConstructorNames($names)
    {
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

    protected function indentLines(array $lines)
    {
        $print = [];

        foreach ($lines as $line) {
            $indent = (int)key($line);
            $keyline = $line[$indent];
            $print[] = sprintf("\n%s%s", str_repeat(' ', $indent), $keyline);
        }

        return implode('', $print);
    }

    protected function getMethodBody(Definition $definition, $id)
    {
        if ($id === $this->container->getName()) {
            return sprintf('%s%sreturn $this->services[$id] = $this;%s', "\n", str_repeat(' ', 8), "\n");
        }

        if ($definition->hasFactory()) {
            $instance = sprintf('$instance = %s', $this->getDefinitionFactory($definition, $this->getServiceArgs($definition->getArguments())));
            $return = sprintf("return \$this->services[\$id] = \$instance;\n");
            return $this->indentLines([[8 => $instance], [8 => $return]]);
        }



        $class = $definition->getClass();
        $constructor = sprintf('new %s(%s)', '\\'.rtrim($class, '\\'), $this->getServiceArgs($definition->getArguments()));

        if ($definition->hasSetters()) {
            $setters = $this->getSetters($definition);
            return <<<EOL
\n        if (!(\$service = \$this->find($id))) {
            \$this->initialized[\$id] = \$service = $constructor;
$setters

        }
        return \$service;

EOL;
        }

        $prefix = ($isProto = ContainerInterface::SCOPE_PROTOTYPE === $definition->getScope()) ? '' : ' $this->services[$id] =';

        return <<<EOL
\n        return$prefix $constructor;\n
EOL;
    }

    protected function getSetters(Definition $definition)
    {
        $callers = [];

        foreach ($definition->getSetters() as $setter) {
            $callers[] = sprintf('%s$service->%s(%s);', str_repeat(' ', 12), $key = key($setter), $this->getServiceArgs($setter[$key]));
        }

        return implode("\n", $callers);
    }

    protected function getServiceArgs(array $arguments)
    {
        $args = [];

        foreach ($arguments as $argument) {
            if ($this->container->isReference($argument)) {
                $id = str_replace(Container::SERVICE_REF_INDICATOR, '', (string)$argument);
                $args[] = sprintf('$this->getService(\'%s\')', $id);
            } elseif (is_array($argument)) {
                $args[] = $this->extractParams($argument);
            } elseif (null === $argument) {
                $args[] = 'null';
            } else {
                $args[] = $foo = var_export($argument, true);
            }
        }

        return implode(', ', $args);
    }

    protected function extractParams(array $params, $indent = 12)
    {
        $array = [];

        foreach ($params as $param => $value) {

            if (is_array($value)) {
                $value = $this->extractParams($value, $indent + 4);
            } else {
                $value = var_export($value, true);
            }

            $array[] = sprintf('%s%s => %s,', str_repeat(' ', $indent), var_export($param, true), $value);

        }
        return empty($array) ? '[]' : preg_replace('#\d+ \=\>\s?#i', '', sprintf("[\n%s\n%s]", implode("\n", $array), str_repeat(' ', $indent - 4)));
        //return preg_replace('#\d+ \=\>\s?#i', '', sprintf("[\n%s\n%s]", implode("\n", $array), str_repeat(' ', $indent - 4)));
    }

    protected function getDefaultClassMethods()
    {
        $parameters = $this->extractParams($this->container->getParameters()->all());

        return <<<EOL

    public function getAlias(\$id)
    {
        return \$this->getDefault(\$this->aliases, \$id, \$id);
    }

    /**
     * {@iniheritdoc}
     */
    public function getService(\$id)
    {
        if (isset(\$this->services[\$id = \$this->getAlias(\$id)])) {
            return \$this->services[\$id];
        }

        if (isset(\$this->cmap[\$id])) {
            return call_user_func([\$this, \$this->cmap[\$id]], \$id);
        }

        return parent::getService(\$id);
    }

    /**
     * {@iniheritdoc}
     */
    public function hasService(\$id)
    {
        return array_key_exists(\$id, \$this->cmap);
    }

    protected function getDefaultParameters()
    {
        return new LockedParameters($parameters);
    }

    protected function find(\$id)
    {
        return isset(\$this->initialized[\$id]) ? \$this->initialized[\$id] : false;
    }
EOL;
    }

    protected function add($string)
    {
        $this->contents[] = $string;
    }

    protected function getClassProperties()
    {

    }

    /**
     * getConstuctor
     *
     * @access protected
     * @return mixed
     */
    protected function getConstuctor()
    {
        return <<<EOL
    public function __construct()
    {
        \$this->aliases = [];
        \$this->initialized = [];
        \$this->parameters = \$this->getDefaultParameters();
        \$this->cmap = \$this->getContructorsMap();
    }
EOL;
    }

    protected function getDefinitionFactory(Definition $definition, $args)
    {
        $factory = $definition->getFactory();

        if ($factory instanceof SerializableClosure) {
            $method = sprintf("(unserialize('%s'))", str_replace(["'"], ["\\'"], serialize($factory)));
        } else {
            $method = sprintf('[%s::%s]');
        }

        return sprintf('call_user_func_array(%s, [%s]);', $method, $args);
    }

    /**
     * startClass
     *
     * @param mixed $class
     * @param mixed $parentClass
     * @param mixed $namespace
     *
     * @access protected
     * @return mixed
     */
    protected function open($class, $parentClass, $namespace = null)
    {
        $namespace = $namespace ? sprintf('%snamespace %s;%s', "\n", $namespace, "\n") : '';
        $date = date('D m Y, H:i:s');
        return <<<EOF
<?php

/**
 * This file was autogeneradted at $date,
 * and is part of the Selene Framework.
 *
 * @canonical <https://github.com/seleneapp.git>
 *
 */
$namespace
use \Selene\Components\DI\Reference;
use \Selene\Components\DI\Container;
use \Selene\Components\DI\Parameters;
use \Selene\Components\DI\LockedParameters;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\Common\Traits\Getter;

/**
 *
 * $class
 * @see $parentClass
 */
class $class extends $parentClass
{
    use Getter;
EOF;

    }

    protected function close()
    {
        return <<<EOF
}\n
EOF;
    }
}
