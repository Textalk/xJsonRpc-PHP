<?php

namespace Textalk\JsonRpc;

/**
 * Invalid method parameter(s).
 */
class InvalidParamsError extends Exception
{
    public function __construct($data = null)
    {
        parent::__construct(self::INVALID_PARAMS, 'Invalid params', $data);
    }
}
