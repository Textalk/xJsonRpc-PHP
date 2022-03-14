<?php

namespace Textalk\JsonRpc;

/**
 * Internal JSON-RPC error.
 */
class InternalError extends Exception
{
    public function __construct()
    {
        parent::__construct(self::INTERNAL_ERROR, 'Internal error');
    }
}
