<?php

/**
 * This File is part of the Selene\Components\View\Template package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\View\Template;

use \Selene\Components\Common\Helper\StringHelper;

/**
 * @class TemplateResolver implements ResolverInterface
 * @see ResolverInterface
 *
 * @package Selene\Components\View\Template
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class Resolver implements ResolverInterface
{
    /**
     * resolve
     *
     * @param mixed $template
     *
     * @access public
     * @return TemplateInterface
     */
    public function resolve($template)
    {
        return $template instanceof TemplateInterface ? $template : $this->parseToTemplate($template);
    }

    /**
     * parseToTemplate
     *
     * @param mixed $template
     *
     * @access protected
     * @return TemlateInterface
     */
    protected function parseToTemplate($template)
    {
        if (0 === substr_count($template, '.')) {
            return new Template($template);
        }
        try {
            list ($name, $engine) = array_pad(StringHelper::strrposSplit($template, '.'), 2, null);

            return new Template($name, $engine);

        } catch (\Exception $e) {
            throw new \InvalidArgumentException('invalid template '.(string)$template);
        }
    }
}
