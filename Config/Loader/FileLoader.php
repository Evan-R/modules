<?php

/**
 * This File is part of the Selene\Components\Config\Loader package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Config\Loader;

use \Selene\Components\Config\Resource\LocatorInterface;

/**
 * @class FileLoader
 * @package Selene\Components\Config\Loader
 * @version $Id$
 */
abstract class FileLoader extends AbstractLoader
{

    /**
     * locator
     *
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * extension
     *
     * @var string
     */
    protected $extension;

    /**
     * Constructor.
     *
     * @param LocatorInterface $locator
     */
    public function __construct(LocatorInterface $locator)
    {
        $this->locator = $locator;
    }

    /**
     * {@inhertidoc}
     */
    public function supports($resource)
    {
        return is_string($resource) && $this->extension ===  pathinfo(strtolower($resource), PATHINFO_EXTENSION);

    }

    /**
     * findResourceOrigin
     *
     * @access protected
     * @return string|array
     */
    protected function findResource($resource, $any = self::LOAD_ONE)
    {
        return $this->locator->locate($resource, $any);
    }
}
