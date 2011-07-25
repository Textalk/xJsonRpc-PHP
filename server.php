<?php
function is_assoc(array $array)
{
    foreach($array as $key => $value)
    {
        if(!is_numeric($key))
            return true;
    }
    return false;
}
class JsonrpcException extends Exception
{
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

        return json_encode($data);
    }
}

/**
 * Invalid JSON was received by the server.
 * An error occurred on the serverrver while parsing the JSON text
 */
class JsonrpcParseError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(-32700, "Parse error");
    }
}

/**
 * The JSON sent is not a valid Request object
 */
class JsonrpcInvalidRequestError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(-32600, "Invalid request");
    }
}

/**
 * The JSON-RPC call is using an incompatible version
 */
class JsonrpcInvalidVersionError extends JsonrpcException
{
    public function __construct()
    {
        parent::__construct(0, "Incompatible version", array(expectedVersion => "2.0"));
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
    }
}

class ApplicationError extends JsonrpcException
{
}

class Jsonrpc20Server
{
    public function handle()
    {

    }
}
?>
