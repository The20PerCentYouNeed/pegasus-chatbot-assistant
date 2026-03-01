<?php

if (!function_exists('pegasusClient')) {
    function pegasusClient()
    {
        return app('pegasus_client');
    }
}
