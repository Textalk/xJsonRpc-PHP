<?php

namespace Textalk\JsonRpc;

/**
 * The JSON-RPC call is using an incompatible version
 */
class InvalidVersionError extends Exception
{
    public function __construct()
    {
        parent::__construct(0, 'Incompatible version', ['expectedVersion' => '2.0']);
    }
}
