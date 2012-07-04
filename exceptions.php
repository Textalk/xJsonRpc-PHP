<?php
class JsonrpcException extends Exception
{
    const
        PARSE_ERROR = -32700,
        INVALID_REQUEST = -32600,
        METHOD_NOT_FOUND = -32601,
        INVALID_PARAMS = -32602,
        INTERNAL_ERROR = -32603;

    protected $data;

    public function __construct($code, $message, $data = NULL)
    {
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getDict()
    {
        $data = array(
            "code" => $this->getCode(),
            "message" => $this->getMessage()
        );
        if($this->data)
            $data["data"] = $this->data;

        return $data;
    }
}

/**
 * Invalid JSON was received by the server.
 * An error occurred on the server while parsing the JSON text
 */
class JsonrpcParseError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(self::PARSE_ERROR, "Parse error");
    }
}

/**
 * Invalid JSON was received FROM the server.
 */
class JsonrpcParseResponseError extends JsonrpcException
{
    private $response;

    public function __construct($response)
    {
        parent::__construct(0, "Response parse error");
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

/**
 * The JSON sent is not a valid Request object
 */
class JsonrpcInvalidRequestError extends JsonrpcException
{
    public function __construct($message)
    {
        parent::__construct(self::INVALID_REQUEST, "Invalid request: " . $message);
    }
}

/**
 * The JSON-RPC call is using an incompatible version
 */
class JsonrpcInvalidVersionError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(0, "Incompatible version", array('expectedVersion' => "2.0"));
    }
}

/**
 * The method does not exist / is not available.
 */
class JsonrpcMethodNotFoundError extends JsonrpcException
{
    public function __construct($method = null)
    {
        if (empty($method)) parent::__construct(self::METHOD_NOT_FOUND, "Method not found");
        else parent::__construct(self::METHOD_NOT_FOUND, "Method not found: $method");
    }
}

/**
 * Invalid method parameter(s).
 */
class JsonrpcInvalidParamsError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(self::INVALID_PARAMS, "Invalid params");
    }
}

/**
 * Internal JSON-RPC error.
 */
class JsonrpcInternalError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(self::INTERNAL_ERROR, "Internal error");
    }
}

class JsonrpcApplicationError extends JsonrpcException
{
}
?>
