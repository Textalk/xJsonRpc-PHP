<?php
class JsonrpcException extends Exception
{
    protected $data;
    protected $http_status = 200;

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

    public function getHttpStatus() {
      return $this->http_status;
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
        parent::__construct(-32700, "Parse error");
        $this->http_status = 500;
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
        parent::__construct(-32600, "Invalid request: " . $message);
        $this->http_status = 400;
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
        $this->http_status = 500;
    }
}

/**
 * The method does not exist / is not available.
 */
class JsonrpcMethodNotFoundError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(-32601, "Method not found");
        $this->http_status = 404;
    }
}

/**
 * Invalid method parameter(s).
 */
class JsonrpcInvalidParamsError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(-32602, "Invalid params");
        $this->http_status = 500;
    }
}

/**
 * Internal JSON-RPC error.
 */
class JsonrpcInternalError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(-32603, "Internal error");
        $this->http_status = 500;
    }
}

class JsonrpcApplicationError extends JsonrpcException
{
}
?>
