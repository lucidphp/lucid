<?php

namespace Lucid\Cache\Driver;

function extension_loaded($ext)
{
    if (in_array($ext, ['apc', 'apcu', 'xcache'])) {
        return true;
    }

    return call_user_func_array('extension_loaded', func_get_args());
}
