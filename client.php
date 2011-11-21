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
         $request_json = $this->json_encode($request);

         $curl = curl_init($this->endpoint);

         curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);

         $result = curl_exec($curl);
         return $this->parse_result($result);
     }

     protected function parse_result($result_json)
     {
          echo $result_json;
     }

    protected function json_encode($request)
    {
        return json_encode($request);
    }
}
?>
