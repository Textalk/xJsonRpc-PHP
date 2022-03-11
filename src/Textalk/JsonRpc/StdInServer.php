<?php

namespace Textalk\JsonRpc;

class StdInServer extends Server
{
    public function handle($data)
    {
        $data = file_get_contents('php://stdin');
        return parent::handle($data);
    }
}
