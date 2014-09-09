<?php

/*
 * This File is part of the Selene\Module\Routing\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Routing\Exception;

/**
 * @class FilterException
 *
 * @package Selene\Module\Routing\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class FilterException extends \Exception
{
    public function __construct($message, $response)
    {
        $this->response = $response;

        parent::__construct($message);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public static function blockedRequest($response)
    {
        return new self(sprintf('Request was blocked by a filter', $response));
    }
}
