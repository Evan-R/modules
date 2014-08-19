<?php

/*
 * This File is part of the Selene\Module\Routing package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing;

use \Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @class Redirect
 * @package Selene\Module\Routing
 * @version $Id$
 */
class Redirect
{
    /**
     * url
     *
     * @var UrlBuilder
     */
    private $url;

    /**
     * data
     *
     * @var array
     */
    private $data;

    /**
     * Constructor.
     *
     * @param UrlBuilder $url
     */
    public function __construct(UrlBuilder $url)
    {
        $this->url = $url;
    }

    /**
     * withErrors
     *
     * @param array $errors
     *
     * @return self
     */
    public function withErrors(array $errors)
    {
        $this->data['errors'] = $errors;

        return $this;
    }

    /**
     * withMessages
     *
     * @param array $errors
     *
     * @return self
     */
    public function withMessages(array $errors)
    {
        $this->data['messages'] = $messages;

        return $this;
    }

    /**
     * withData
     *
     * @param array $data
     *
     * @return self
     */
    public function withData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * redirectUrl
     *
     * @param string $url
     * @param string $type
     *
     * @return self
     */
    public function redirectUrl($url, $status = 302, $host = null)
    {
        $response = $this->createResponse($url, $status);
    }

    /**
     * redirectRoute
     *
     * @param string $route
     * @param int    $status
     * @param array  $arguments
     * @param string $host
     *
     * @return self
     */
    public function redirectRoute($route, $status = 302, array $arguments = [], $host = null)
    {
        $url = $this->url->getPath(
            $route,
            $arguments,
            UrlBuilder::RELATIVE_PATH,
            //null !== $host ? UrlBuilder::RELATIVE_ABSOLUTE : UrlBuilder::RELATIVE_PATH,
            $host
        );

        var_dump($url);

        $response = $this->createResponse($url, $status);

        return $response;
    }

    /**
     * createResponse
     *
     * @param mixed $url
     * @param mixed $status
     * @param array $headers
     *
     * @return RedirectResponse
     */
    protected function createResponse($url, $status, array $headers = [])
    {
        $response = new RedirectResponse($url, $status, $headers);

        $this->flushData($response);

        return $response;
    }

    /**
     * flushData
     *
     * @param RedirectResponse $response
     *
     * @return void
     */
    protected function flushData(RedirectResponse $response)
    {
        $data = $this->data;

        if ($this->url->getRequest()->getSession()) {
            // $response->setSession()
            // flash the data to the session.
        }

        $this->data = null;
    }
}
