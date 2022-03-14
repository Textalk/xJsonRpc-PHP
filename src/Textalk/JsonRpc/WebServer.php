<?php

namespace Textalk\JsonRpc;

class WebServer extends Server
{
    public function handle($data)
    {
        header('Content-type: application/json-rpc');
        $data = file_get_contents('php://input');
        $result = $this->handleRequest($data);

        header('HTTP/1.0 200'); // Signifying that the communication went well.

        return $this->encodeJson($result);
    }
}
