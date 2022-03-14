<?php

namespace Textalk\JsonRpc;

class WebClientNotify
{
    protected $parent;

    public function __construct(WebClient $parent)
    {
        $this->parent = $parent;
    }

    public function __call($method, $args)
    {
        return $this->parent->notify($method, $args);
    }
}
