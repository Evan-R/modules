<?php

/**
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing;

use \Serializable;
use \Selene\Module\Common\Traits\Getter;

/**
 * @class Route implements Serializable
 * @see Serializable
 *
 * @package Selene\Module\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class Route implements Serializable
{
    use Getter {
        Getter::getDefault as protected getDefaultVar;
    }

    protected $collections;
    /**
     * Route name
     *
     * @var string
     */
    protected $name;

    /**
     * Route pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * parent
     *
     * @var string
     */
    private $parent;

    /**
     * requirements
     *
     * @var array
     */
    protected $requirements;

    /**
     * compiledArgs
     *
     * @var array
     */
    protected $compiledArgs;

    /**
     * parameters
     *
     * @var array
     */
    protected $parameters;

    /**
     * defaults
     *
     * @var array
     */
    protected $defaults;
    /**
     * compiled
     *
     * @var boolean
     */
    protected $compiled;

    /**
     * Constructor.
     *
     * @param string       $name
     * @param string       $pattern
     * @param string|array $methods
     * @param string       $host
     * @param array        $requirements
     * @param array        $parameters
     */
    public function __construct($name, $pattern, $methods = 'GET', array $requirements = [])
    {
        $this->setName($name);
        $this->collections = [];

        $this->pattern = $pattern;

        $this->initRequirements($requirements);

        $this->defaults       = ['route' => [], 'host' => []];
        $this->parameters     = [];
        $this->compiledArgs   = [];

        $this->setMethods((array)$methods);
    }

    /**
     * Add before filter names
     *
     * @param string $filter
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function filterBefore($filter)
    {
        $this->requirements['_before'][] = $filter;

        return $this;
    }

    /**
     * filterAfter
     *
     * @param string $filter
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function filterAfter($filter)
    {
        $this->requirements['_after'][] = $filter;

        return $this;
    }

    /**
     * check if a parmeter is optional
     *
     * @param string $parameter
     *
     * @return boolean returns always false if route is not compiled
     */
    public function parameterIsOptional($parameter)
    {
        if (!$this->isCompiled()) {
            return false;
        }

        $vars = (array)$this->getVars();

        if (!in_array($parameter, $vars)) {
            return false;
        }

        foreach ($this->getDefaultVar($this->compiledArgs, 'tokens') as $token) {
            if ('variable' === $token[0] && true === $token[4]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the before filters
     *
     * @param mixed|array $filters
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setBeforeFilters($filters)
    {
        if (is_string($filters)) {
            $filters = explode('|', $filters);
        }

        $this->requirements['_before'] = [];

        foreach ((array)$filters as $filter) {
            if (!in_array($filter, $this->requirements['_before'])) {
                $this->requirements['_before'][] = trim((string)$filter);
            }
        }

        return $this;
    }

    /**
     * Gets the before filters
     *
     * @return array
     */
    public function getBeforeFilters()
    {
        return (array)$this->getDefaultVar($this->requirements, '_before', []);
    }

    /**
     * Sets the after filters
     *
     * @param string|array $filters
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setAfterFilters($filters)
    {
        if (is_string($filters)) {
            $filters = explode('|', $filters);
        }

        $this->requirements['_after'] = [];

        foreach ((array)$filters as $filter) {
            if (!in_array($filter, $this->requirements['_after'])) {
                $this->requirements['_after'][] = trim((string)$filter);
            }
        }

        return $this;
    }

    /**
     * Gets the after filters
     *
     * @return array
     */
    public function getAfterFilters()
    {
        return (array)$this->getDefaultVar($this->requirements, '_after', []);
    }

    /**
     * Sets the route name.
     *
     * @param string $name
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setName($name)
    {
        if ($this->isCompiled()) {
            throw new \BadMethodCallException('Cannot changed name on a compiled route.');
        }

        if (!empty($this->collections)) {
            foreach ($this->collections as $collection) {
                unset($collection[$this->name]);
                $collection->add($this);
            }
        }

        $this->name = $name;

        return $this;
    }

    /**
     * getName
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * collection
     *
     * @param RouteCollectionInterface $collection
     *
     * @return void
     */
    public function collection(RouteCollectionInterface $collection)
    {
        $this->collections[] = &$collection;
    }

    /**
     * setMethods
     *
     * @param array $methods
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setMethods(array $methods)
    {
        $this->requirements['_methods'] = array_map('strtoupper', $methods);
        return $this;
    }

    /**
     * getMethods
     *
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->getDefaultVar($this->requirements, '_methods', []);
    }

    /**
     * setAction
     *
     * @param string $action
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setAction($action)
    {
        $this->requirements['_action'] = $action;

        return $this;
    }

    /**
     * getAction
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getDefaultVar($this->requirements, '_action');
    }

    /**
     * setHostParameters
     *
     * @param array $params
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHostParameters(array $params)
    {
        foreach ((array)$this->getHostVars() as $var) {
            if ($val = $this->getDefaultVar($params, $var, false)) {
                $this->hostParameters[$var] = $val;
            }
        }
        return $this;
    }

    /**
     * setParameters
     *
     * @param array $params
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setParameters(array $params)
    {
        foreach ((array)$this->getVars() as $var) {
            if ($val = $this->getDefaultVar($params, $var, false)) {
                $this->parameters[$var] = $val;
            }
        }

        return $this;
    }

    /**
     * getParameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->getParams();
    }

    /**
     * getParameters
     *
     * @return array
     */
    public function getHostParameters()
    {
        return $this->getHostParams();
    }

    /**
     * setDefaults
     *
     * @param array $defaults
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults['route'] = $defaults;

        return $this;
    }

    /**
     * setDefault
     *
     * @param string $var
     * @param mixed $value
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setDefault($var, $value)
    {
        $this->defaults['route'][$var] = $value;

        return $this;
    }

    /**
     * setHostDefaults
     *
     * @param array $defaults
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHostDefaults(array $defaults)
    {
        $this->defaults['host'] = $defaults;

        return $this;
    }

    /**
     * setHostDefault
     *
     * @param string $var
     * @param mixed $value
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHostDefault($var, $value)
    {
        $this->defaults['host'][$var] = $value;

        return $this;
    }

    /**
     * hasDefault
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasDefault($key)
    {
        return array_key_exists($key, $this->defaults['route']);
    }

    /**
     * hasHostDefault
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasHostDefault($key)
    {
        return array_key_exists($key, $this->defaults['host']);
    }

    /**
     * getDefaults
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults['route'];
    }

    /**
     * getHostDefaults
     *
     * @return array
     */
    public function getHostDefaults()
    {
        return $this->defaults['host'];
    }

    /**
     * getHostDefault
     *
     * @param string $var
     *
     * @return mixed
     */
    public function getHostDefault($var)
    {
        return $this->getDefaultVar($this->defaults['host'], $var);
    }

    /**
     * getDefault
     *
     * @param string $var
     *
     * @return mixed
     */
    public function getDefault($var)
    {
        return $this->getDefaultVar($this->defaults['route'], $var);
    }

    /**
     * setRequirement
     *
     * @param string $requirement
     * @param mixed $value
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setRequirement($requirement, $value)
    {
        $req = '_'.strtolower(trim($requirement, '_'));

        $this->requirements[$req] = $value;

        return $this;
    }

    /**
     * getRequirement
     *
     * @param string $requirement
     *
     * @return mixed
     */
    public function getRequirement($requirement)
    {
        $req = '_'.strtolower(trim($requirement, '_'));

        return $this->getDefaultVar($this->requirements, $req);
    }

    /**
     * getSchemes
     *
     *
     * @return array
     */
    public function getSchemes()
    {
        return $this->getDefaultVar($this->requirements, '_schemes', []);
    }

    /**
     * setSchemes
     *
     * @param array $schemes
     *
     * @return Route
     */
    public function setSchemes(array $schemes)
    {
        $this->requirements['_schemes'] = $schemes;

        return $this;
    }

    /**
     * isSecure
     *
     * @return mixed
     */
    public function isSecure()
    {
        $schemes = $this->getDefaultVar($this->requirements, '_schemes', []);

        foreach (['https', 'spdy'] as $protocoll) {
            if (in_array($protocoll, $schemes)) {
                return true;
            }
        }

        return false;
    }

    /**
     * setConstraint
     *
     * @param string $param
     * @param mixed $regexp
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setConstraint($param, $regexp)
    {
        return $this->setParamConstraint($param, $regexp);
    }

    /**
     * getConstraint
     *
     * @param string $param
     * @param string $regexp
     *
     * @return string
     */
    public function getConstraint($param)
    {
        return $this->getParamConstraint($param);
    }

    /**
     * setHostConstraint
     *
     * @param string $param
     * @param string $regexp
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHostConstraint($param, $regexp)
    {
        $this->requirements['_constraints']['host'][$param] = $regexp;

        return $this;
    }

    /**
     * setParent
     *
     * @param string $parent
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * setHost
     *
     * @param string $host
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHost($host = null)
    {
        $this->requirements['_host'] = $host;

        return $this;
    }

    /**
     * setParamConstraint
     *
     * @param string $param
     * @param string $regexp
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setParamConstraint($param, $regexp)
    {
        $this->requirements['_constraints']['route'][strtolower($param)] = $regexp;

        return $this;
    }

    /**
     * getPattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * getRequirements
     *
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * getParamConstraint
     *
     * @param string $param
     *
     * @return string
     */
    public function getParamConstraint($param)
    {
        return $this->getDefaultArray($this->requirements['_constraints'], 'route.' . $param, null);
    }

    /**
     * getHostConstraint
     *
     * @param string $param
     *
     * @return string
     */
    public function getHostConstraint($param)
    {
        return $this->getDefaultArray($this->requirements['_constraints'], 'host.'. $param, null);
    }

    /**
     * getParent
     *
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * getHost
     *
     *
     * @return string
     */
    public function getHost()
    {
        return $this->requirements['_host'];
    }

    /**
     * hasHost
     *
     *
     * @return boolean
     */
    public function hasHost()
    {
        return (bool)$this->getHost();
    }

    /**
     * hasMethod
     *
     * @param string $method
     *
     * @return boolean
     */
    public function hasMethod($method)
    {
        return in_array(strtolower($method), $this->getMethods());
    }

    /**
     * isCompiled
     *
     *
     * @return boolean
     */
    public function isCompiled()
    {
        return (bool)$this->compiled;
    }

    /**
     * compile
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function compile()
    {
        if (null === $this->getAction()) {
            throw new \BadMethodCallException(
                sprintf('cannot compile route %s. A route action is not set.', $this->name)
            );
        }

        if (!$this->isCompiled()) {
            $this->setCompiledResults(call_user_func($this->getCompileMethod(), $this));
            $this->compiled = true;
        }

        return $this;
    }

    /**
     * setCompiledResults
     *
     * @param array $results
     *
     * @return void
     */
    private function setCompiledResults(array $results)
    {
        $this->compiledArgs = $results;
    }

    /**
     * getTokens
     *
     * @return array
     */
    public function getTokens()
    {
        if (!$this->isCompiled()) {
            return [];
        }

        return $this->compiledArgs['tokens'];
    }

    /**
     * getVars
     *
     * @return string
     */
    public function getVars()
    {
        return $this->getDefaultVar($this->compiledArgs, 'vars', []);
    }

    /**
     * getHostVars
     *
     *
     * @return array
     */
    public function getHostVars()
    {
        return $this->getDefaultArray($this->compiledArgs, 'host.vars', []);
    }

    /**
     * setParams
     *
     * @param array $params
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setParams(array $params)
    {
        if ($this->isCompiled()) {
            $this->parameters['route'] = $params;
        }

        return $this;
    }

    /**
     * setHostParams
     *
     * @param array $params
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHostParams(array $params)
    {
        if ($this->isCompiled()) {
            $this->parameters['host'] = $params;
        }

        return $this;
    }

    /**
     * getParams
     *
     * @return string
     */
    public function getParams()
    {
        $defaults = $this->getDefaults();

        return array_merge($defaults, $this->getDefaultVar($this->parameters, 'route', []));
    }

    /**
     * getHostParams
     *
     * @return array
     */
    public function getHostParams()
    {
        return $this->getDefaultVar($this->parameters, 'host', []);
    }

    /**
     * getHostRegexp
     *
     * @return string
     */
    public function getHostRegexp()
    {
        return $this->getDefaultArray($this->compiledArgs, 'host.regexp');
    }

    /**
     * getRegexp
     *
     * @return string
     */
    public function getRegexp()
    {
        return $this->getDefaultVar($this->compiledArgs, 'regexp');
    }

    /**
     * getStaticPath
     *
     *
     * @return string
     */
    public function getStaticPath()
    {
        return $this->getDefaultVar($this->compiledArgs, 'static_path');
    }

    /**
     * getCompileMethod
     *
     * @return string
     */
    public function getCompileMethod()
    {
        return 'Selene\Module\Routing\RouteCompiler::compile';
    }

    /**
     * __clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->parameters = [];
    }

    /**
     * serialize
     *
     * @return string
     */
    public function serialize()
    {
        if (!$this->isCompiled()) {
            $this->compile();
        }

        $data = [
            'name'          => $this->name,
            'pattern'       => $this->pattern,
            'parent'        => $this->parent,
            'defaults'      => $this->defaults,
            'parameters'    => $this->parameters,
            'requirements'  => $this->requirements,
            'compiled_args' => $this->compiledArgs,
        ];

        return serialize($data);
    }

    /**
     * unserialize
     *
     * @param array $data
     *
     * @return \Selene\Component\Routing\Route this instance
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->name         = $data['name'];
        $this->parent       = $data['parent'];
        $this->pattern      = $data['pattern'];
        $this->defaults     = $data['defaults'];
        $this->parameters   = $data['parameters'];
        $this->requirements = $data['requirements'];
        $this->compiledArgs = $data['compiled_args'];

        $this->compiled = true;

        return $this;
    }

    /**
     * initRequirements
     *
     * @return void
     */
    protected function initRequirements(array $requirements = [])
    {
        $this->requirements = [];

        foreach ($requirements as $requirements => $value) {
            $this->setRequirement($requirements, $value);
        }

        $this->requirements = array_merge(
            [
                '_schemes' => ['http'],
                '_host' => null,
                '_before' => [],
                '_after' => [],
                '_constraints' => []
            ],
            $this->requirements
        );
    }
}
