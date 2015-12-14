<?php

/*
 * This File is part of the Lucid\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http;

use Psr\Http\Message\ResponseInterface as PsrResponse;

/**
 * @interface ResponseInterface
 *
 * @package Lucid\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ResponseInterface extends PsrResponse
{
    const STATUS_CONTINUE                 = 100; // => 'Continue',
    const STATUS_SWITCH_PROTOCOL          = 101; // => 'Switching Protocols',
    const STATUS_PROCESSING               = 102; // => 'Processing',
    const STATUS_OK                       = 200; // => 'Ok',
    const STATUS_CREATED                  = 201; // => 'Created',
    const STATUS_ACCEPTED                 = 202; // => 'Accepted',
    const STATUS_TENTATIVE_INFO           = 203;
    const STATUS_NO_CONTENT               = 204; // => 'No Content',
    const STATUS_REQUEST_CONTENT          = 205; // => 'Reset Content',
    const STATUS_PARTIAL_CONTENT          = 206; // => 'Partial Content',
    const STATUS_MULTI                    = 207; // => 'Multi-Status',
    const STATUS_ALLREADY_REPORTED        = 208; // => 'Already Reported',
    const STATUS_IM_USED                  = 226; // => 'IM Used',
    const STATUS_MULTI_CHOICE             = 300; // => 'Multiple Choices',
    const STATUS_MOVED_PERMANENT          = 301; // => 'Moved Permanently',
    const STATUS_FOUND                    = 302; // => 'Found',
    const STATUS_SEE_OTHER                = 303; // => 'See Other',
    const STATUS_NOT_MODIFIED             = 304; // => 'Not Modified',
    const STATUS_USE_PROXY                = 305; // => 'Use Proxy',
    const STATUS_SWITCH_PROXY             = 306; // => 'Switch Proxy',
    const STATUS_TEMP_REDIRECT            = 307; // => 'Temporary Redirect',
    const STATUS_PERM_REDIRECT            = 308; // => 'Permanent Redirect',
    const STATUS_BAD_REQUEST              = 400; // => 'Bad Request',
    const STATUS_UNAUTHORIZED             = 401; // => 'Unauthorized',
    const STATUS_PAYMENT_REQUIRED         = 402; // => 'Payment Required',
    const STATUS_FORBIDDEN                = 403; // => 'Forbidden',
    const STATUS_NOT_FOUND                = 404; // => 'Not Found',
    const STATUS_METHOD_NOT_ALLOWED       = 405; // => 'Method Not Allowed',
    const STATUS_NOT_ACCEPTABLE           = 406; // => 'Not Acceptable',
    const STATUS_PROXY_AUTH_REQUIRED      = 407; // => 'Proxy Authentication Required',
    const STATUS_REQUEST_TIMEOUT          = 408; // => 'Request Time-out',
    const STATUS_CONFLICT                 = 409; // => 'Conflict',
    const STATUS_GONE                     = 410; // => 'Gone',
    const STATUS_LENGTH_REQUIRED          = 410; // => 'Length Required',
    const STATUS_PRECONDITION_FAILED      = 412; // => 'Precondition Failed',
    const STATUS_REQUEST_ENTITY_EXCEEDS   = 413; // => 'Request Entity Too Large',
    const STATUS_REQUEST_URL_EXCEEDS      = 414; // => 'Request-URL Too Long',
    const STATUS_UNSUPPORTED_MEDIA        = 415; // => 'Unsupported Media Type',
    const STATUS_BAD_REQUEST_RANGE        = 416; // => 'Requested range not satisfiable',
    const STATUS_BAD_EXPECTATION          = 417; // => 'Expectation Failed',
    const STATUS_TEAPOT                   = 418; // => 'I\'m a teapot',
    const STATUS_POLICY_UNFULFILLED       = 420; // => 'Policy Not Fulfilled',
    const STATUS_CONNECTIONS_EXCEEDS      = 421; // => 'There are too many connections from your internet address',
    const STATUS_UNPROCESSABLE_ENTITY     = 422; // => 'Unprocessable Entity',
    const STATUS_LOCKED                   = 423; // => 'Locked',
    const STATUS_DEPENDENCY_FAILED        = 424; // => 'Failed Dependency',
    const STATUS_UNORDERED_COLLECTION     = 425; // => 'Unordered Collection',
    const STATUS_UPGRADE_REQUIRED         = 426; // => 'Upgrade Required',
    const STATUS_REQUESTS_EXCEEDS         = 429; // => 'Too Many Requests',
    const STATUS_NO_RESPONSE              = 444; // => 'No Response',
    const STATUS_RETRY_REQUEST            = 449; // => 'The request should be retried after doing the appropriate action',
    const STATUS_LEGAL_RESTRICTED         = 451; // => 'Unavailable For Legal Reasons',
    const STATUS_ERROR                    = 500; // => 'Internal Server Error',
    const STATUS_NOT_IMPLEMENTED          = 501; // => 'Not Implemented',
    const STATUS_BAD_GATEWAY              = 502; // => 'Bad Gateway',
    const STATUS_UNAVAILABLE              = 503; // => 'Service Unavailable',
    const STATUS_TIMEOUT                  = 504; // => 'Gateway Time-out',
    const STATUS_HTTP_UNSUPPORTED         = 505; // => 'HTTP Version not supported',
    const STATUS_VARIANT_NEGOTIATION      = 506; // => 'Variant Also Negotiates',
    const STATUS_INSUFFICIENT_STORAGE     = 507; // => 'Insufficient Storage',
    const STATUS_BANDWIDTH_LIMIT_EXCEEDED = 509; // => 'Bandwidth Limit Exceeded',
    const STATUS_NOT_EXTENDED             = 510; // => 'Not Extended',
    const STATUS_NETWORK_AUTH_REQUIRED    = 511; // => 'Network Authentication Required',

    /**
     * Set the response contents
     *
     * @param string|array $content the response content as string.
     *
     * @return void
     */
    public function setContent($content);

    /**
     * Sends response headers and content to the client
     *
     * @return void
     */
    public function send();

    /**
     * Sends response headers to the client.
     *
     * @return void
     */
    public function sendHeaders();

    /**
     * Sends response content to the client.
     *
     * @return void
     */
    public function sendContent();

    /**
     * Current status is Ok.
     *
     * @return bool
     */
    public function isOk();

    /**
     * Current status is error.
     *
     * @return bool
     */
    public function isError();

    /**
     * Current status is client error.
     *
     * @return bool
     */
    public function isClientError();

    /**
     * Current status is server error.
     *
     * @return bool
     */
    public function isServerError();

    /**
     * Current status is redirect.
     *
     * @return bool
     */
    public function isRedirect();

    /**
     * Current status is unknowen.
     *
     * @return bool
     */
    public function isStatusUnknowen();
}
