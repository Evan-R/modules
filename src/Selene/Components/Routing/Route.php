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
 * @class Route
 * @package Selene\Components\Routing
 * @version $Id$
 */
class Route implements Serializable
{
    use Getter {
        Getter::getDefault as protected getDefaultVar;
    }

    /**
     * name
     *
     * @var string
     */
    protected $name;

    /**
     * path
     *
     * @var string
     */
    protected $path;

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
     * @param string       $path
     * @param string|array $methods
     * @param string       $host
     * @param array        $requirements
     * @param array        $parameters
     *
     * @access public
     */
    public function __construct($name, $path, $methods = 'GET', array $requirements = [])
    {
        $this->name = $name;
        $this->path = $path;

        $this->initRequirements($requirements);

        $this->defaults       = ['route' => [], 'host' => []];
        $this->parameters     = [];
        $this->compiledArgs   = [];

        $this->setMethods((array)$methods);
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
        foreach (explode('|', $filters) as $filter) {
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
        return $this->getDefaultVar($this->requirements, '_before');
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
        foreach (explode('|', $filters) as $filter) {
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
        return $this->getDefaultVar($this->requirements, '_after');
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
        //if (!isset($this->requirements['_constraints']['host'])) {
        //    $this->requirements['_constraints']['host'] = [];
        //}

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
     * getPath
     *
     * @access public
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
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

    /**
     * getVars
     *
     * @access public
     * @return string
     */
    public function getVars()
    {
        return $this->getDefaultVar($this->compiledArgs, 'vars');
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
        return $this->getDefaultArray($this->compiledArgs, 'host.vars');
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
            'path'          => $this->path,
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
        $this->path         = $data['path'];
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
        return;
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
                '_host' => null,
                '_before' => [],
                '_after' => [],
                '_constraints' => []
            ],
            $this->requirements
        );
    }
}
