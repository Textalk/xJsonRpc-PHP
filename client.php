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

    public function notify($method, $args)
    {
        $request = $this->assemble_request($method, $args, true);
        return $this->json_encode($request);
    }

    protected function assemble_request($method, $args, $notification = false)
    {
        return array(
            'method'  => $method,
            'params'  => $args,
            'id'      => $notification ? null : 1,
            'jsonrpc' => '2.0'
        );
    }

    protected function send_request(array $request)
    {
        $request_json = $this->_encode_json($request);

        $curl = curl_init($this->endpoint);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);

        $result = curl_exec($curl);

        return $this->_delegate_response($result);
    }

    protected function _handle_success_response($response, $reqid)
    {
        // ...
    }

    protected function _handle_error_response($response, $reqid)
    {
        return $response;
    }

    protected function _delegate_response($result)
    {
        if(is_assoc($result))
            return $this->_handle_response($result);
        else
            return $this->_handle_batch_response($result);
    }

    protected function _handle_batch_response($responses)
    {
        $result = array();
        foreach($responses as $response)
            $result[] = $this->_hande_response($response);

        return $result;
    }

    protected function _handle_response ($response)
    {
        try
        {
            $this->_validate_response($response);
        }
        catch(JsonrpcException $e)
        {
            return $this->_handle_error_response($e, NULL);
        }

        try
        {

        }
        catch(JsonrpcException $e)
        {

        }
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
