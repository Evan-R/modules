<?php

/**
 * This File is part of the Selene\Module\View\Composer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\View\Composer;

use \Selene\Module\View\RendererInterface;
use \Selene\Module\View\DispatcherInterface as View;

/**
 * @class Composer
 * @package Selene\Module\View\Composer
 * @version $Id$
 */
abstract class Composer implements ComposerInterface
{
    /**
     * context
     *
     * @var array
     */
    protected $composables;

    /**
     * compose
     *
     * @param string $template
     * @param Composable $context
     *
     * @return void
     */
    public function addComposable($template, Composable $composable)
    {
        if ($this->hasService($template)) {
            throw new \InvalidArgumentException(
                sprintf('A composer for template %s already exists as service', $template)
            );
        }

        $this->composables[(string)$template] = &$composable;
    }

    /**
     * has
     *
     * @param mixed $template
     *
     * @return boolean
     */
    public function has($template)
    {
        if (isset($this->composables[(string)$template])) {
            return true;
        }

        try {
            $res = $this->getService($template);

            return (bool)$res;
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * compose
     *
     * @param RendererInterface $renderer
     *
     * @access public
     * @return mixed
     */
    public function compose(RendererInterface $renderer)
    {
        if (!$this->has($template = $renderer->getTemplateName())) {
            return;
        }

        try {
            $res = $this->getService($template);
        } catch (\Exception $e) {
            $res = $this->composables[(string)$template];
        }

        $res->compose(new Context($renderer));
    }

    abstract public function setService($template, $id);

    abstract public function getService($template);

    abstract public function hasService($template);
}
