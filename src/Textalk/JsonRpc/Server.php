<?php

namespace Textalk\JsonRpc;

/**
 * @TODO Handle incorrect amount of arguments (Well lack of arguments)
 */
class Server
{
    public function handle($data)
    {
        $result = $this->handleRequest($data);
        return $this->encodeJson($result);
    }

    protected function handleRequest($data)
    {
        try {
            $request = $this->parseJson($data);
        } catch (ParseError $e) {
            return $this->createErrorResponse($e, null);
        }
        return $this->delegateRequest($request);
    }

    protected function parseJson($json)
    {
        $result = json_decode($json, true);
        if ($result === null) {
            throw new ParseError();
        }
        return $result;
    }

    protected function encodeJson($result)
    {
        return json_encode($result);
    }

    protected function createSuccessResponse($result, $reqid)
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $reqid,
            'result' => $result
        ];
    }

    protected function createErrorResponse(Exception $error, $reqid)
    {
        $response = [
            'jsonrpc' => '2.0',
            'id' => $reqid,
        ];
        if ($error instanceof Exception) {
            $response['error'] = $error->getDict();
        } else {
            $response['error'] = [
                'code' => 0,
                'message' => $error->getMessage()
            ];
        }
        return $response;
    }

    protected function delegateRequest($request)
    {
        if (Helper::isAssoc($request)) {
            return $this->parseRequest($request);
        } else {
            return $this->parseBatchRequest($request);
        }
    }

    protected function parseBatchRequest($requests)
    {
        $result = [];
        foreach ($requests as $request) {
            $request_result = $this->parseRequest($request);

            // Don't collect notification responses
            if ($request_result !== null) {
                $result[] = $request_result;
            }
        }

        return $result;
    }

    protected function parseRequest($request)
    {
        try {
            $this->validateRequest($request);
        } catch (Exception $e) {
            return $this->createErrorResponse($e, null);
        }

        try {
            $result = $this->runRequest($request);
            if (isset($request['id'])) {
                return $this->createSuccessResponse($result, $request['id']);
            } else {
                return null; // No return data for notification requests
            }
        } catch (Exception $e) {
            return $this->createErrorResponse($e, $request['id']);
        }
    }

    protected function validateRequest($request)
    {
        if (!array_key_exists('method', $request)) {
            throw new InvalidRequestError('Missing method');
        }

        if (!array_key_exists('jsonrpc', $request) || $request['jsonrpc'] != '2.0') {
            throw new InvalidVersionError();
        }

        if (!is_string($request['method'])) {
            throw new InvalidRequestError('Method is not string but ' . gettype($request['method']));
        }

        if (array_key_exists('params', $request) && !is_array($request['params'])) {
            throw new InvalidRequestError('Params is not array but ' . gettype($request['params']));
        }

        if (
            isset($request['id']) && !is_numeric($request['id']) && !is_string($request['id'])
            && !is_null($request['id'])
        ) {
            throw new InvalidRequestError('id isn\'t string, int or NULL but ' . gettype($request['id']));
        }

        return true;
    }

    protected function runRequest($request)
    {
        $reqid = $request['id'];
        $method = trim($request['method']);
        $params = array_key_exists('params', $request) ? $request['params'] : [];

        try {
            $caller = new \ReflectionMethod($this, $method);
        } catch (\ReflectionException $e) {
            throw new MethodNotFoundError($method);
        }
        // Only public methods defined in extending class are allowed to be call
        if (!$caller->isPublic() || $caller->getDeclaringClass()->name == 'Textalk\JsonRpc\Server') {
            throw new MethodNotFoundError($method);
        }
        return $caller->invokeArgs($this, $params);
    }
}
