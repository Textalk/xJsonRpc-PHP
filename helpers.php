<?php
if (!function_exists('is_assoc')) {
    function is_assoc(array $array)
    {
        foreach($array as $key => $value)
        {
            if(!is_numeric($key))
                return true;
        }
        return false;
    }
}
?>
