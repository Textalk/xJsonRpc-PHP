<?php

namespace Textalk\JsonRpc;

class BatchRequest
{
    protected $client;
    protected $calls;
    protected $result;

    public function __construct(WebClient $client)
    {
        $this->client = $client;
        $this->calls = [];
    }

    public function __call($method, $args)
    {
        $reqid = count($this->calls);
        $this->calls[] = $this->client->assembleRequest($method, $args, $reqid);

        return $reqid;
    }

    public function __invoke()
    {
        $this->result = $this->client->sendRequest($this->calls);
        return $this->result;
    }
}
