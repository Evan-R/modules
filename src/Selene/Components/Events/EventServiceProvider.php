<?php

/**
 * This File is part of the Selene\Components\Events package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Events;

/**
 * @class EventServiceProvider
 * @package
 * @version $Id$
 */
class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * register
     *
     * @param ContainerInterface $container
     * @param array $parameters
     *
     * @access public
     * @return void
     */
    public function register(ContainerInterface $container, array $parameters = [])
    {
        $container->singleton('events', __NAMESPACE__.'\\Dispatcher');
    }

    /**
     * boot
     *
     * @access public
     * @return mixed
     */
    public function boot()
    {

    }

    /**
     * provides
     *
     * @access public
     * @return array
     */
    public static function provides()
    {
        return ['events'];
    }
}
