<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux\Tests\Request\Fixures package
 *
 * (c) iwyg <mail@thomas-appel.com> 
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Tests\Request\Fixures;

/**
 * @class Server
 *
 * @package Lucid\Mux\Tests\Request\Fixures
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Server
{
    public static function mock(array $server = [])
    {
        $time = microtime(true);
        return array_merge([
          'HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4,it;q=0.2',
          'HTTP_ACCEPT_ENCODING' => 'gzip, deflate, sdch',
          'HTTP_DNT' => '1',
          'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36',
          'HTTP_UPGRADE_INSECURE_REQUESTS' => '1',
          'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
          'HTTP_CACHE_CONTROL' => 'max-age=0',
          'HTTP_CONNECTION' => 'keep-alive',
          'HTTP_HOST' => '192.168.99.100',
          'REDIRECT_STATUS' => 200,
          'SERVER_NAME' => 'default',
          'SERVER_PORT' => 80,
          'SERVER_ADDR' => '172.17.0.6',
          'REMOTE_PORT' => '51543',
          'REMOTE_ADDR' => '192.168.99.1',
          'SERVER_SOFTWARE' => 'nginx/1.9.9',
          'GATEWAY_INTERFACE' => 'CGI/1.1',
          'REQUEST_SCHEME' => 'http',
          'SERVER_PROTOCOL' => 'HTTP/1.1',
          'DOCUMENT_ROOT' => '/var/www/app/public',
          'DOCUMENT_URI' => '/index.php',
          'REQUEST_URI' => '/',
          'SCRIPT_NAME' => '/index.php',
          'CONTENT_LENGTH' => '',
          'CONTENT_TYPE' => '',
          'REQUEST_METHOD' => 'GET',
          'QUERY_STRING' => '',
          'SCRIPT_FILENAME' => '/var/www/app/public/index.php',
          'FCGI_ROLE' => 'RESPONDER',
          'PHP_SELF' => '/index.php',
          'REQUEST_TIME_FLOAT' => $time,
          'REQUEST_TIME' => (int)$time,
        ], $server);
    }
}
