<?php

/**
 * This File is part of the Selene\Components\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Routing;

use \Serializable;
use \Selene\Components\Common\Traits\Getter;

/**
 * @class Route implements Serializable
 * @see Serializable
 *
 * @package Selene\Components\Routing
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Route implements Serializable
{
    use Getter {
        Getter::getDefault as protected getDefaultVar;
    }

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
    protected $parent;

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
     *
     * @param string       $name
     * @param string       $pattern
     * @param string|array $methods
     * @param string       $host
     * @param array        $requirements
     * @param array        $parameters
     *
     * @access public
     */
    public function __construct($name, $pattern, $methods = 'GET', array $requirements = [])
    {
        $this->name = $name;
        $this->pattern = $pattern;

        $this->initRequirements($requirements);

        $this->defaults       = ['route' => [], 'host' => []];
        $this->parameters     = [];
        $this->compiledArgs   = [];

        $this->setMethods((array)$methods);
    }

    public function filterBefore($filter)
    {
        if (!is_string($filter)) {
            return;
        }

        $this->requirements['_before'][] = $filter;

        return $this;
    }

    public function filterAfter($filter)
    {
        if (!is_string($filter)) {
            return;
        }

        $this->requirements['_after'][] = $filter;

        return $this;
    }

    public function parameterIsOptional($parameter)
    {
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
     * addBeforeFilter
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
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
     * getBeforeFilters
     *
     * @access public
     * @return array
     */
    public function getBeforeFilters()
    {
        return (array)$this->getDefaultVar($this->requirements, '_before', []);
    }

    /**
     * setAfterFilters
     *
     * @param array $filters
     *
     * @access public
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
     * getAfterFilters
     *
     * @access public
     * @return mixed
     */
    public function getAfterFilters()
    {
        return (array)$this->getDefaultVar($this->requirements, '_after', []);
    }

    /**
     * setName
     *
     * @param mixed $name
     *
     * @access public
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * getName
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * setMethods
     *
     * @param array $methods
     *
     * @access public
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
     * @access public
     * @return array
     */
    public function getMethods()
    {
        return $this->getDefaultVar($this->requirements, '_methods', []);
    }

    /**
     * setAction
     *
     * @param mixed $action
     *
     * @access public
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
     * @access public
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
     * @access public
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
     * @access public
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
     * @access public
     * @return array
     */
    public function getParameters()
    {
        return $this->getParams();
    }

    /**
     * getParameters
     *
     * @access public
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
     * @access public
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
     * @param mixed $var
     * @param mixed $value
     *
     * @access public
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
     * @access public
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
     * @param mixed $var
     * @param mixed $value
     *
     * @access public
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setHostDefault($var, $value)
    {
        $this->defaults['host'][$var] = $value;

        return $this;
    }

    /**
     * getDefaults
     *
     * @access public
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults['route'];
    }

    public function getHostDefaults()
    {
        return $this->defaults['host'];
    }

    public function getHostDefault($var)
    {
        return $this->getDefaultVar($this->defaults['host'], $var);
    }

    /**
     * getDefault
     *
     * @param mixed $var
     *
     * @access public
     * @return mixed
     */
    public function getDefault($var)
    {
        return $this->getDefaultVar($this->defaults['route'], $var);
    }

    /**
     * setRequirement
     *
     * @param mixed $requirement
     * @param mixed $value
     *
     * @access public
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
     * @param mixed $requirement
     *
     * @access public
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
     * @access public
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
     * @access public
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
     * @access public
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
     * @param mixed $param
     * @param mixed $regexp
     *
     * @access public
     * @return \Selene\Component\Routing\Route this instance
     */
    public function setConstraint($param, $regexp)
    {
        return $this->setParamConstraint($param, $regexp);
    }

    /**
     * getConstraint
     *
     * @param mixed $param
     * @param mixed $regexp
     *
     * @access public
     * @return mixed
     */
    public function getConstraint($param)
    {
        return $this->getParamConstraint($param);
    }

    /**
     * setHostConstraint
     *
     * @param mixed $param
     * @param mixed $regexp
     *
     * @access public
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
     * @param mixed $parent
     *
     * @access public
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
     * @param mixed $host
     *
     * @access public
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
     * @param mixed $param
     * @param mixed $regexp
     *
     * @access public
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
     * @access public
     * @return mixed
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * getRequirements
     *
     * @access public
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * getParamConstraint
     *
     * @param mixed $param
     *
     * @access public
     * @return string
     */
    public function getParamConstraint($param)
    {
        return $this->getDefaultArray($this->requirements['_constraints'], 'route.' . $param, null);
    }

    /**
     * getHostConstraint
     *
     * @param mixed $param
     *
     * @access public
     * @return mixed
     */
    public function getHostConstraint($param)
    {
        return $this->getDefaultArray($this->requirements['_constraints'], 'host.'. $param, null);
    }

    /**
     * getParent
     *
     *
     * @access public
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
     * @access public
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
     * @access public
     * @return boolean
     */
    public function hasHost()
    {
        return (bool)$this->getHost();
    }

    /**
     * hasMethod
     *
     * @param mixed $method
     *
     * @access public
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
     * @access public
     * @return boolean
     */
    public function isCompiled()
    {
        return (bool)$this->compiled;
    }

    /**
     * compile
     *
     * @access public
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
     * @access private
     * @return void
     */
    private function setCompiledResults(array $results)
    {
        $this->compiledArgs = $results;
    }

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
     * @access public
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
     * @access public
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
     * @access public
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
     * @access public
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
     * @access public
     * @return mixed
     */
    public function getParams()
    {
        $defaults = $this->getDefaults();
        return array_merge($defaults, $this->getDefaultVar($this->parameters, 'route', []));
    }

    /**
     * getHostParams
     *
     * @access public
     * @return mixed
     */
    public function getHostParams()
    {
        return $this->getDefaultVar($this->parameters, 'host', []);
    }

    /**
     * getHostRegexp
     *
     * @access public
     * @return string
     */
    public function getHostRegexp()
    {
        return $this->getDefaultArray($this->compiledArgs, 'host.regexp');
    }

    /**
     * getRegexp
     *
     * @access public
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
     * @access public
     * @return string
     */
    public function getStaticPath()
    {
        return $this->getDefaultVar($this->compiledArgs, 'static_path');
    }

    /**
     * getCompileMethod
     *
     * @access public
     * @return string
     */
    public function getCompileMethod()
    {
        return 'Selene\Components\Routing\RouteCompiler::compile';
    }

    /**
     * __clone
     *
     * @access public
     * @return void
     */
    public function __clone()
    {
        $this->parameters = [];
    }

    /**
     * serialize
     *
     * @access public
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
     * @access public
     * @return \Selene\Component\Routing\Route this instance
     */
    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->name         = $data['name'];
        $this->pattern      = $data['pattern'];
        $this->parent       = $data['parent'];
        $this->defaults     = $data['defaults'];
        $this->parameters   = $data['parameters'];
        $this->requirements = $data['requirements'];
        $this->compiledArgs = $data['compiled_args'];

        $this->compiled = true;

        return $this;
    }

    /**
     * methodDict
     *
     * @param mixed $method
     *
     * @access protected
     * @return mixed
     */
    protected static function methodDict($method)
    {
        return [
            'GET'    => ['GET', 'HEAD'],
            'HEAD'   => ['GET', 'HEAD'],
            'POST'   => ['POST'],
            'PUT'    => ['PUT', 'PATCH'],
            'PATCH'  => ['PUT', 'PATCH'],
            'DELETE' => ['DELETE']
        ];
    }

    /**
     * initRequirements
     *
     * @access protected
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
