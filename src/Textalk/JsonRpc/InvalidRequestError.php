<?php

namespace Textalk\JsonRpc;

/**
 * The JSON sent is not a valid Request object
 */
class InvalidRequestError extends Exception
{
    public function __construct($message)
    {
        parent::__construct(self::INVALID_REQUEST, 'Invalid request: ' . $message);
    }
}
