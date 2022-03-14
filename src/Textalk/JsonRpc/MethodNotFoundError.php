<?php

namespace Textalk\JsonRpc;

/**
 * The method does not exist / is not available.
 */
class MethodNotFoundError extends Exception
{
    public function __construct($method = null)
    {
        if (empty($method)) {
            parent::__construct(self::METHOD_NOT_FOUND, 'Method not found');
        } else {
            parent::__construct(self::METHOD_NOT_FOUND, "Method not found: $method");
        }
    }
}
