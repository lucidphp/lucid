<?php

namespace Lucid\Module\Http\Session\Storage;

function session_start()
{
    if (!isset($_SESSION)) {
        $_SESSION = $GLOBALS['session'];
    }

    $GLOBALS['sess_stat'] = PHP_SESSION_ACTIVE;

    return isset($GLOBALS['sess_start_fail']) ? false : true;
}

function session_status()
{
    return $GLOBALS['sess_stat'];
}

function session_name($name = null)
{
    if (null !== $name) {
        unset($GLOBALS['sess_name']);
        $GLOBALS['sess_name'] = $name;
    }

    if (null === $GLOBALS['sess_name']) {
        $GLOBALS['sess_name'] = '_NATIVE_SESS_TEST_';
    }

    return $GLOBALS['sess_name'];
}

function session_id($id = null)
{
    if (null !== $id) {
        unset($GLOBALS['sess_id']);
        $GLOBALS['sess_id'] = $id;
    }

    if (null === $GLOBALS['sess_id']) {
        $GLOBALS['sess_id'] = 'rand'.(string)rand(10000, 99999);
    }

    return $GLOBALS['sess_id'];
}

function session_write_close()
{
    unset($GLOBALS['sess_stat']);
    $GLOBALS['sess_stat'] = PHP_SESSION_NONE;
}

function session_register_shutdown()
{
}
