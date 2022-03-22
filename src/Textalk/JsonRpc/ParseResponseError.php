<?php

namespace Textalk\JsonRpc;

/**
 * Invalid JSON was received FROM the server.
 */
class ParseResponseError extends Exception
{
    private $response;

    public function __construct($response)
    {
        parent::__construct(0, 'Response parse error');
        $this->response = $response;
    }

    /**
     * To debug what couldn't be parsed.
     * @return The response that couldn't be parsed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
