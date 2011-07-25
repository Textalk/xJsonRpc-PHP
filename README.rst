Description
===========

xJsonRPC-PHP is a JSON-RPC library for PHP featuring a client(TODO) and a server. Currently it follows the 2.0 spec of JSON-RPC, including batch calls

Usage
=====

Server
------

There's currently 3 server implementations: Jsonrpc20Server, WebJsonrpc20Server and StdinJsonrpc20Server

Jsonrpc20Server
...............

Base class, accepts a JSON string as argument to handle() that is processed as a JSON-RPC request

WebJsonrpc20Server
..................

Will attempt to read out php://input to get the JSON-RPC request, handle() should be called (Without arguments) to make it start processing 

StdinJsonrpc20Server
....................

Does the same as WebJsonrpc20Server except it uses php://stdin (Command line, etc.) instead of php://input

Implementing methods
....................

To implement methods you subclass one of the above servers and add methods prepended with jsonrpc20_ any method with that prefix can be called through JSON-RPC (Leaving out the jsonrpc20_ prefix of course)

Example
.......

::

    require_once('xjsonrpc-php/server.php');
    
    class ExampleServer extends WebJsonrpc20Server
    {
        public function jsonrpc20_echo($echo)
        {
            return $echo;
        }
    }

    $server = ExampleServer();
    $server->handle() 

And then you can call the echo method with your preferred JSON-RPC client library by connecting to whatever url you have set-up that calls ``$server->handle()``

Client
------

TODO
