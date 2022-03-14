<?php

namespace Textalk\JsonRpc;

/**
 * @TODO Error handling
 */
class WebClient
{
    // Bitfield flags to constructor
    public const NO_VERIFY_SSL = 1;

    protected $endpoint;
    protected $debug = false;
    protected $verify_ssl = true;
    public $notify;

    public function __construct($endpoint, $flags = 0)
    {
        $this->endpoint = $endpoint;
        $this->notify = new WebClientNotify($this);

        if ($flags & self::NO_VERIFY_SSL) {
            $this->verify_ssl = false;
        }
    }

    public function __call($method, $args)
    {
        $request = $this->assembleRequest($method, $args);
        return $this->sendRequest($request);
    }

    public function createBatchRequest()
    {
        return new BatchRequest($this);
    }

    public function notify($method, $args)
    {
        $request = $this->assembleRequest($method, $args, null, true);
        return $this->encodeJson($request);
    }

    public function assembleRequest($method, $args, $reqid = 1, $notification = false)
    {
        return [
            'method'  => $method,
            'params'  => $args,
            'id'      => $notification ? null : $reqid,
            'jsonrpc' => '2.0'
        ];
    }

    public function sendRequest(array $request)
    {
        $request_json = $this->encodeJson($request);

        $curl = curl_init($this->endpoint);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);

        if ($this->verify_ssl === false) {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        if ($this->debug) {
            trigger_error("Jsonrpc/WebClient sending request: $request_json");
        }
        $response = curl_exec($curl);

        return $this->parse($response);
    }

    public function setDebug($debug = true)
    {
        $this->debug = $debug;
    }

    protected function parse($data)
    {
        $response = $this->parseJson($data);
        return $this->delegateResponse($response);
    }

    protected function handleSuccessResponse($response, $reqid)
    {
        return [
            'result' => $response,
            'reqid'  => $reqid
        ];
    }

    protected function handleErrorResponse(Exception $error, $reqid)
    {
        return [
            'result' => $error,
            'reqid'  => $reqid
        ];
    }

    protected function delegateResponse($response)
    {
        if (Helper::isAssoc($response)) {
            $result = $this->handleResponse($response);
            $result = $result['result'];

            if ($result instanceof Exception) {
                throw $result;
            } else {
                return $result;
            }
        } else {
            return $this->handleBatchResponse($response);
        }
    }

    protected function handleBatchResponse($responses)
    {
        $results = [];
        foreach ($responses as $response) {
            $result = $this->handleResponse($response);
            $results[$result['reqid']] = $result['result'];
        }

        return $results;
    }

    protected function handleResponse($response)
    {
        try {
            $this->validateResponse($response);
        } catch (Exception $exception) {
            return $this->handleErrorResponse($exception, null);
        }

        try {
            $result = $this->parseResponse($response);
            return $this->handleSuccessResponse($result, $response['id']);
        } catch (Exception $exception) {
            return $this->handleErrorResponse($exception, $response['id']);
        }
    }

    protected function parseResponse($response)
    {
        if (array_key_exists('error', $response)) {
            $error = $response['error'];
            switch ($error['code']) {
                case -32700:
                    throw new ParseError();
                    break;
                case -32600:
                    throw new InvalidRequestError($error['message']);
                    break;
                case -32601:
                    throw new MethodNotFoundError();
                    break;
                case -32602:
                    throw new InvalidParamsError();
                    break;
                case -32603:
                    throw new InternalError();
                    break;
                case 0:
                    //throw new InvalidVersionError();
                    break;
                default:
                    $data = array_key_exists('data', $error) ? $error['data'] : null;
                    throw new ApplicationError($error['code'], $error['message'], $data);
                    break;
            }
        }

        return $response['result'];
    }

    protected function validateResponse($response)
    {
        if (!array_key_exists('id', $response)) {
            throw new InvalidRequestError('Missing id');
        }

        //if(!array_key_exists('jsonrpc', $response) || $response['jsonrpc'] != '2.0')
        //    throw new InvalidVersionError();

        if (!array_key_exists('result', $response) && !array_key_exists('error', $response)) {
            throw new InvalidRequestError('No error or result in response');
        }

        if (!is_numeric($response['id']) && !is_string($response['id']) && !is_null($response['id'])) {
            throw new InvalidRequestError('id isn\'t string or int but ' . gettype($request['id']));
        }

        return true;
    }

    protected function parseJson($json)
    {
        $result = json_decode($json, true);
        if ($result === null) {
            throw new ParseResponseError($json);
        }

        return $result;
    }

    protected function encodeJson($result)
    {
        return json_encode($result);
    }
}
