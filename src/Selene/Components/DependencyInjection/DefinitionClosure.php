<?php

/**
 * This File is part of the Selene\Components\DependencyInjection package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DependencyInjection;

use Closure;
use ReflectionFunction;

/**
 * @class ClosureDefinition
 * @package
 * @version $Id$
 */
class DefinitionClosure extends Definition
{
    private $closure;

    protected $isDeferred = true;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function getClosure()
    {
        return $this->closure;
    }

    /**
     * __invoke
     *
     * @access public
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array($this->closure, func_get_args());
    }

    public function inspect()
    {
        $reflection = new ReflectionFunction($this->closure);
        $funcString = $this->exportClosureBody($reflection);

        $vars = $this->getUseScopeVaiables($reflection, $funcString);

    }

    private function exportClosureBody(ReflectionFunction $reflection)
    {
        $file = new \SplFileObject($reflection->getFileName());

        $file->seek($reflection->getStartLine() - 1);
        $closure = '';

        while ($file->key() < $reflection->getEndLine()) {
            $closure .= $file->current();
            $file->next();
        }

        return substr($closure, $start = strpos($closure, 'function'), strrpos($closure, '}') - $start + 1);
    }

    private function getUseScopeVaiables(ReflectionFunction $reflection, $closure)
    {
        $vars = [];

        if (!$useStart = stripos($closure, 'use')) {
            return $vars;
        }

        return $reflection->getStaticVariables();

        //$start = $start = strpos($closure, '(', $useStart) + 1;
        //$end   = strpos($closure, ')', $start);
        //$use   = explode(',', str_replace(' ', null, substr($closure, $start, $end - $start)));

        //foreach ($use as $variable) {
        //    $vars[$v = ltrim($variable, '&$')] = $staticVars[$v];
        //}
    }
}
