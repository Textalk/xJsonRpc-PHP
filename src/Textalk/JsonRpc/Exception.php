<?php

namespace Textalk\JsonRpc;

class Exception extends \Exception
{
    public const
        PARSE_ERROR = -32700,
        INVALID_REQUEST = -32600,
        METHOD_NOT_FOUND = -32601,
        INVALID_PARAMS = -32602,
        INTERNAL_ERROR = -32603;

    protected $data;

    public function __construct($code, $message, $data = null)
    {
        $this->data = $data;
        parent::__construct($message, $code);
    }

    public function getDict()
    {
        $data = [
            'code' => $this->getCode(),
            'message' => $this->getMessage()
        ];
        if ($this->data) {
            $data['data'] = $this->data;
        }
        return $data;
    }

    public function getData()
    {
        return $this->data;
    }
}
