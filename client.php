<?php
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
        $this->endpoint = $endpoints;
        $this->notify = Jsonrpc20WebClientNotify($this);
    }

    public function __call($method, $args)
    {
        $request = $this->assemble_request($method, $args);
        return $this->json_encode($request);
    }

    public function notify($method, $args)
    {
        $request = $this->assemble_request($method, $args, true);
        return $this->json_encode($request);
    }

    protected function assemble_request($method, $args, $notification = false)
    {
    }

    protected function json_encode($request)
    {
        return json_encode($request);
    }
}
?>
