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

use \Selene\Writer\Writer;
use \Selene\Writer\Generator\GeneratorInterface;
use \Selene\Writer\Generator\Object\Method;
use \Selene\Writer\Generator\Object\Property;
use \Selene\Writer\Generator\Object\Argument;
use \Selene\Writer\Generator\Object\ClassGenerator;
use \Selene\Components\DI\ContainerInterface;
use \Selene\Components\DI\Dumper\Object\ServiceMethod;
use \Selene\Components\DI\Dumper\Object\ServiceMethodBody;
use \Selene\Components\DI\Dumper\Object\ReturnStatement;
use \Selene\Components\DI\Dumper\Traits\FormatterTrait;

/**
 * @class Generator
 * @package Selene\Components\DI\Dumper
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class ContainerGenerator implements GeneratorInterface
{
    use FormatterTrait;

    /**
     * containerServiceName
     *
     * @var string
     */
    protected $serviceId;

    /**
     * container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * imports
     *
     * @var array
     */
    protected $imports;

    /**
     * traits
     *
     * @var array
     */
    protected $traits;

    /**
     * processed
     *
     * @var boolean
     */
    protected $processed;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     * @param string $namespace
     * @param string $className
     * @param string $containerServiceName
     */
    public function __construct(ContainerInterface $container, $namespace, $className, $id = null)
    {
        $this->traits    = [];
        $this->imports   = [];
        $this->serviceId = $id;
        $this->container = $container;
        $this->processes = false;

        $this->className = $className;
        $this->namespace = $namespace;

        $this->importResolver = new ImportResolver;

        $this->cg = new ClassGenerator($className, $namespace, '\\' . get_class($container));
    }

    /**
     * setServiceId
     *
     * @param string $id
     *
     * @return void
     */
    public function setServiceId($id)
    {
        $this->processed = false;

        $this->serviceId = $id;
    }

    /**
     * addImort
     *
     * @param string $import
     *
     * @return void
     */
    public function addImort($import)
    {
        $this->processed = false;

        $this->imports[] = $import;
    }

    /**
     * addTrait
     *
     * @param string $trait
     *
     * @return void
     */
    public function addTrait($trait)
    {
        $this->processed = false;

        $this->traits[] = $trait;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $this->process();

        $result = $this->cg->generate($raw);

        return $result;
    }

    /**
     * Builds the class on the ClassGenerator.
     *
     * @return void
     */
    protected function process()
    {
        if ($this->processed) {
            return;
        }

        $this->cg->setUseStatements($this->getImports());
        $this->cg->setTraits($this->getTraits());

        $this->setProperties();
        $this->setConstructor();
        $this->setDefaultClassMethods();
        $this->setServiceMethods();
        $this->setParameterMethods();

        $this->processed = true;
    }

    /**
     * setProperties
     *
     * @return void
     */
    protected function setProperties()
    {
        $this->cg->addProperty(new Property('cmap', Property::IS_PRIVATE, Property::T_ARRAY));
        $this->cg->addProperty(new Property('icmap', Property::IS_PRIVATE, Property::T_ARRAY));
    }

    /**
     * setConstructor
     *
     * @return void
     */
    protected function setConstructor()
    {
        $this->cg->addMethod($method = new Method('__construct'));

        $method->setDocComment('Constructor.');
        $method->setBody(
            (new Writer)
            ->writeln('$this->parameters  = new StaticParameters($this->getDefaultParams());')
            ->writeln('$this->aliases     = new Aliases($this->getDefaultAliases());')
            ->writeln('$this->cmap        = $this->getConstructorMap();')
            ->writeln('$this->icmap       = $this->getInternalContructorsMap();')
            ->newline()
            ->writeln('$this->synced      = [];')
            ->writeln('$this->services    = [];')
            ->writeln('$this->definitions = [];')
            ->writeln('$this->injected    = [];')
            ->writeln('$this->building    = [];')
        );
    }

    /**
     * setServiceMethods
     *
     * @return void
     */
    protected function setServiceMethods()
    {
        foreach ($this->container->getDefinitions() as $id => $definition) {

            if ($definition->isAbstract()) {
                continue;
            }

            $this->cg->addMethod($method = new ServiceMethod($this->container, $id));

            // if the id is the container itself, set the appropriate body.
            if ($this->serviceId === $id) {
                $definition->setClass($this->namespace . '\\', $this->className);
                $method->setBody((string)(new ReturnStatement('$this->services[\''.$id.'\'] = $this', 0)));

                continue;
            }

            // Resolve the class alias according to previous imports
            $this->importResolver->add($class = $definition->getClass());
            // Set the method body and inject the service class alias.
            $method->setBody(new ServiceMethodBody($this->container, $id, $this->importResolver->getAlias($class)));
            // Set the resolved import statement on the container.
            $this->cg->addUseStatement($this->importResolver->getImport($class));
        }
    }

    /**
     * setParameterMethods
     *
     * @return void
     */
    protected function setParameterMethods()
    {
        $names    = [];
        $internal = [];
        $aliases  = [];

        $parameters = $this->container->getParameters()->all();

        foreach ($this->container->getDefinitions() as $id => $definition) {
            if ($definition->isInternal()) {
                $internal[$id] = ServiceMethod::getServiceGetterName($id, $definition->isInjected(), true);
            } else {
                $names[$id] = ServiceMethod::getServiceGetterName($id, $definition->isInjected(), false);
            }
        }

        foreach ($this->container->getAliases() as $alias => $id) {
            $aliases[$alias] = (string)$id;
        }

        ksort($names);
        ksort($internal);
        ksort($parameters);
        ksort($aliases);

        $map    = $this->extractParams($names, 0);
        $imap   = $this->extractParams($internal, 0);
        $params = $this->extractParams($parameters, 0);
        $ids    = $this->extractParams($aliases, 0);

        $this->cg->addMethod($method = new Method('getConstructorMap', Method::IS_PRIVATE));
        $method->setBody((string)(new ReturnStatement($map)));

        $this->cg->addMethod($method = new Method('getInternalContructorsMap', Method::IS_PRIVATE));
        $method->setBody((string)(new ReturnStatement($imap)));

        $this->cg->addMethod($method = new Method('getDefaultParams', Method::IS_PRIVATE));
        $method->setBody((string)(new ReturnStatement($params)));

        $this->cg->addMethod($method = new Method('getDefaultAliases', Method::IS_PRIVATE));
        $method->setBody((string)(new ReturnStatement($ids)));
    }

    /**
     * setDefaultClassMethods
     *
     * @return void
     */
    protected function setDefaultClassMethods()
    {
        //// method: ContainerInterface::getAlias():
        //$this->cg->addMethod($method = new Method('getAlias', Method::IS_PUBLIC, Method::T_STRING));

        //$method->addArgument(new Argument('id', Method::T_STRING));
        //$method->setBody((string)(new ReturnStatement('$id')));
        //$method->setBody((string)(new ReturnStatement('$this->getDefault($this->aliases, $id, $id)')));

        // method: ContainerInterface::has():
        $this->cg->addMethod($method = new Method('has', Method::IS_PUBLIC, Method::T_STRING));

        $method->addArgument(new Argument('id', Method::T_STRING));
        $method->setBody((string)(new ReturnStatement('array_key_exists($this->resolveId($id), $this->cmap)')));

        // method: ContainerInterface::get():
        $this->cg->addMethod($method = new Method('get', Method::IS_PUBLIC));

        $method->addArgument(new Argument('id', Method::T_STRING));
        $method->setBody(
            (new Writer)
            ->writeln('$id = $this->resolveId($id);')
            ->newline()
            ->writeln('if (isset($this->services[$id])) {')
            ->indent()
                ->writeln('return $this->services[$id];')
            ->outdent()
            ->writeln('}')
            ->newline()
            ->writeln('if (isset($this->cmap[$id])) {')
            ->indent()
                ->writeln('return call_user_func([$this, $this->cmap[$id]], $id);')
            ->outdent()
            ->writeln('}')
        );

        // method: ContainerInterface::resolveId():
        //$this->cg->addMethod($method = new Method('resolveId', Method::IS_PROTECTED, Method::T_STRING));

        //$method->addArgument(new Argument('id', Method::T_STRING));
        ////$method->setBody((string)(new ReturnStatement('$this->getDefault($this->aliases, $id, $id)')));
        //$method->setBody((string)(new ReturnStatement('$id')));


        // method: ContainerInterface::getInternal():
        $this->cg->addMethod($method = new Method('getInternal', Method::IS_PROTECTED));

        $method->addArgument(new Argument('id', Method::T_STRING));
        $method->setDocComment('Get an internal service');

        $method->setBody(
            (new Writer)
            ->writeln('$id = $this->resolveId($id);')
            ->newline()
            ->writeln('if (isset($this->internals[$id])) {')->indent()
                ->writeln('return $this->internals[$id];')->outdent()
            ->writeln('}')
            ->newline()
            ->writeln('if (isset($this->icmap[$id])) {')->indent()
                ->writeln('return call_user_func([$this, $this->icmap[$id]], $id);')->outdent()
            ->writeln('}')
        );

        // method: ContainerInterface::checkSynced():
        $this->cg->addMethod($method = new Method('checkSynced', Method::IS_PRIVATE, 'void'));

        $comment  = "Post check synced callers\n\n";
        $comment .= "Post check if a setter of a called service that\n";
        $comment .= "has synced dependencies needs to be called immediately.";

        $method->setDocComment($comment);

        $method->addArgument($arg = new Argument('synced', Method::T_ARRAY));

        $arg->isReference(true);

        $method->setBody(
            (new Writer)
            ->writeln('foreach ($synced as $id => $sync) {')->indent()
                ->writeln('if (isset($this->services[$id])) {')->indent()
                    ->writeln('unset($synced[$id]);')->outdent()
                ->writeln('}')->outdent()
            ->writeln('}')
        );
    }

    /**
     * getImports
     *
     * @return array
     */
    protected function getImports()
    {
        return array_unique(array_merge([
            '\Selene\Components\DI\Aliases',
            '\Selene\Components\DI\Container',
            '\Selene\Components\DI\ContainerInterface',
            '\Selene\Components\DI\ParameterInterface',
            '\Selene\Components\DI\StaticParameters',
            '\Selene\Components\Common\Traits\Getter',
        ], $this->imports));
    }

    /**
     * getTraits
     *
     * @return array
     */
    protected function getTraits()
    {
        return array_unique(array_merge([
            '\Selene\Components\Common\Traits\Getter'
        ], $this->traits));
    }
}
