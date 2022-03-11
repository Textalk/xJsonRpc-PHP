<?php

namespace Textalk\JsonRpc;

/**
 * Invalid JSON was received by the server.
 * An error occurred on the server while parsing the JSON text
 */
class ParseError extends Exception
{
    public function __construct()
    {
        parent::__construct(self::PARSE_ERROR, 'Parse error');
    }
}
