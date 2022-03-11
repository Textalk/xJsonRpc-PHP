# Textalk JSON-RPC 2.0

xJsonRPC-PHP is a JSON-RPC library for PHP featuring a client (TODO) and a server. Currently it follows the 2.0 spec of JSON-RPC, including batch calls

## Server

### Usage

There's currently 3 server implementations: Server, WebServer and StdInServer

#### Server

Base class, accepts a JSON string as argument to handle() that is processed as a JSON-RPC request

#### WebServer

Will attempt to read out php://input to get the JSON-RPC request, handle() should be called (without arguments) to make it start processing

#### StdInServer

Does the same as WebServer except it uses php://stdin (command line, etc.) instead of php://input


### Implementing methods


To implement methods you subclass one of the above servers and add methods.
These methods must be public in order to be allowed for server use.

Example
```php
    use \Textalk\JsonRpc\Server;

    class ExampleServer extends Server
    {
        public function echo($echo)
        {
            return $echo;
        }
    }

    $server = ExampleServer();
    $response = $server->handle();
```

And then you can call the echo method with your preferred JSON-RPC client library by connecting to whatever url you have set-up that calls ``$server->handle()``

## Client

TODO
