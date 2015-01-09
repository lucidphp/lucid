<?php

namespace Lucid\Adapter\Filesystem;

use \Lucid\Adapter\Filesystem\Tests\Helper\FtpHelper;

function ftp_ssl_connect($host, $port = 21)
{
    if ('host.fail' === $host) {
        return false;
    }

    return $host;
}

function ftp_connect($host, $port = 21)
{
    return ftp_ssl_connect($host, $port);
}

function ftp_login($resource, $username, $password)
{
    if ('user.fail' === $username || 'pwd.fail' === $password) {
        return false;
    }

    return true;
}

function ftp_pasv($resource, $passive)
{
    return (bool)$passive;
}

function ftp_raw($resource, $cmp)
{
    if (0 === strpos($cmp, 'STAT')) {
        $list = FtpHelper::makeList(substr($cmp, 5));
        var_dump($list);
        return $list;
    }

    return [];
}

function ftp_nlist($ftp, $path)
{
    if (FtpHelper::has($path)) {
        $list = FtpHelper::makeNList($path);
        return $list;
    }

    return false;
}
function ftp_chdir($resource, $dir)
{
    if ('mount.fail' === $dir) {
        trigger_error();
    }

    return true;
}

function ftp_put($ftp)
{
    return false;
}

function ftp_chmod($ftp, $mode, $file)
{
    if ('file.fails' === $file) {
        return false;
    }

    return $mode;
}

function ftp_fput($ftp, $path, $resource, $mode = FTP_BINARY, $start = 0)
{
    if ('file.fails' === $path) {
        return false;
    }

    return true;
}

function get_ftp_stats($path)
{
    $path = $path ?: '';

    if ('textfile.txt' === $path) {
        return [
            'STAT START',
            '-rwxrw-r--   1 user      group           0 Jan 4 4:15 textfile.txt',
            'STAT END'
        ];
    }

    if ('file.fails' === $path) {
        return [
            'STAT START',
            'STAT END'
        ];
    }

    if ('' === $path) {

        return [
            'drwxrw-r--   6 user      group        4096 May 10 4:15  .',
            'drwxrw-r--  14 user      group        4096 May 10 4:15  ..',
            '-rwxrw-r--   1 user      group         128 May 10 4:15  index.html',
            '-rwxrw-r--   1 user      group         128 May 10 4:15  files.html',
            'drwxrw-r--   3 user      group        4096 May 10 4:15  js',
            'drwxrw-r--   5 user      group        4096 May 10 4:15  css',
            'drwxrw-r--  15 user      group        4096 May 10 4:15  images'
        ];
    }
}
