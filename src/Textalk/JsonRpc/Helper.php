<?php

namespace Textalk\JsonRpc;

class Helper
{
    public static function isAssoc(array $array)
    {
        foreach (array_keys($array) as $key) {
            if (!is_int($key)) {
                return true;
            }
        }
        return false;
    }
}
