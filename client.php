<?php
require_once("exceptions.php");
require_once("helpers.php");

class Jsonrpc20WebClientNotify
{
    protected $parent;

    public function __construct(Jsonrpc20WebClient $parent)
    {
        $this->parent = $parent;
    }

    public function __call($method, $args)
    {
        return $this->parent->notify($method, $args);
    }
}

class Jsonrpc20BatchRequest
{
    protected $client;
    protected $calls;
    protected $result;

    public function __construct(Jsonrpc20WebClient $client)
    {
        $this->client = $client;
        $this->calls = array();
    }

    public function __call($method, $args)
    {
        $this->calls[] = $this->client->assemble_request($method, $args, count($this->calls));
    }

    public function __invoke()
    {
        $this->result = $this->client->send_request($this->calls);
        return $this->result;
    }
}

/**
 * @TODO Error handling
 */
class Jsonrpc20WebClient
{
    protected $endpoint;
    public $notify;

    public function __construct($endpoint)
    {
        $this->endpoint = $endpoint;
        $this->notify = new Jsonrpc20WebClientNotify($this);
    }

    public function __call($method, $args)
    {
        $request = $this->assemble_request($method, $args);
        return $this->send_request($request);
    }

    public function create_batch_request()
    {
        return new Jsonrpc20BatchRequest($this);
    }

    public function notify($method, $args)
    {
        $request = $this->assemble_request($method, $args, null, true);
        return $this->json_encode($request);
    }

    public function assemble_request($method, $args, $reqid = 1, $notification = false)
    {
        return array(
            'method'  => $method,
            'params'  => $args,
            'id'      => $notification ? null : $reqid,
            'jsonrpc' => '2.0'
        );
    }

    public function send_request(array $request)
    {
        $request_json = $this->_encode_json($request);

        $curl = curl_init($this->endpoint);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);

        $response = curl_exec($curl);

        return $this->_parse($response);
    }

    protected function _parse($data)
    {
        $response = $this->_parse_json($data);
        return $this->_delegate_response($response);
    }

    protected function _handle_success_response($response, $reqid)
    {
        return array('result' => $response,
                     'reqid'  => $reqid);
    }

    protected function _handle_error_response(Exception $error, $reqid)
    {
        return array('result' => $error,
                     'reqid'  => $reqid);
    }

    protected function _delegate_response($response)
    {
        if(is_assoc($response))
        {
            $result = $this->_handle_response($response);
            $result = $result['result'];

            if($result instanceof Exception)
                throw $result;
            else
                return $result;
        }
        else
            return $this->_handle_batch_response($response);
    }

    protected function _handle_batch_response($responses)
    {
        $results = array();
        foreach($responses as $response)
        {
            $result = $this->_handle_response($response);
            $results[$result['reqid']] = $result['result'];
        }

        return $results;
    }

    protected function _handle_response($response)
    {
        try
        {
            $this->_validate_response($response);
        }
        catch(JsonrpcException $exception)
        {
            return $this->_handle_error_response($exception, NULL);
        }

        try
        {
            $result = $this->_parse_response($response);
            return $this->_handle_success_response($result, $response['id']);
        }
        catch(JsonrpcException $exception)
        {
            return $this->_handle_error_response($exception, $response['id']);
        }
    }

    protected function _parse_response($response)
    {
        if(array_key_exists('error', $response))
        {
            $error = $response['error'];
            switch($error['code'])
            {
                case -32700:
                    throw new JsonrpcParseError();
                    break;
                case -32600:
                    throw new JsonrpcInvalidRequestError();
                    break;
                case -32601:
                    throw new JsonrpcMethodNotFoundError();
                    break;
                case -32602:
                    throw new JsonrpcInvalidParamsError();
                    break;
                case -32603:
                    throw new JsonrpcInternalError();
                    break;
                case 0:
                    throw new JsonrpcInvalidVersionError();
                    break;
                default:
                    throw new JsonrpcApplicationError($error['code'], $error['message']);
                    break;
            }
        }

        return $response['result'];
    }

    protected function _validate_response($response)
    {
        if(!array_key_exists("id", $response))
            throw new JsonrpcInvalidRequestError("Missing id");

        if(!array_key_exists("jsonrpc", $response) || $response["jsonrpc"] != "2.0")
            throw new JsonrpcInvalidVersionError();

        if(!array_key_exists('result', $response) && !array_key_exists('error', $response))
            throw new JsonrpcInvalidRequestError("No error or result in response");

        if(!is_numeric($response["id"]) && !is_string($response["id"]))
            throw new JsonrpcInvalidRequestError("id isn't string or int but " . gettype($request["id"]));

        return true;
    }

    protected function _parse_json($json)
    {
        $result = json_decode($json, true);
        if($result === NULL)
            throw new JsonrpcParseError();

        return $result;
    }
    protected function _encode_json($result)
    {
        return json_encode($result);
    }
}
?>
