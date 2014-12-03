<?php

/*
 * This File is part of the Lucid\Module\Http package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Http;

use RuntimeException;
use InvalidArgumentException;
use Lucid\Module\Http\Response\Body;
use Lucid\Module\Http\Response\Headers;

/**
 * @class Response
 *
 * @package Lucid\Module\Http
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Response implements ResponseInterface
{
    public $headers;
    protected $contents;
    protected $body;
    protected $status;
    protected $reason;
    protected $version;
    protected $sent;

    /**
     * Constructor.
     *
     * @param string $content
     * @param mixed $status
     * @param array $headers
     *
     * @return void
     */
    public function __construct($content = '', $status = self::STATUS_OK, array $headers = [])
    {
        $this->contents = $content;
        $this->setStatus($status);
        $this->headers = new Headers($headers);
        $this->setProtocolVersion('1.1');
    }

    /**
     * {@inheritdoc}
     */
    public function send()
    {
        if ($this->responseSent()) {
            throw new RuntimeException('Cannot send response, response already sent.');
        }

        $this->prepare();
        $this->sendContent();
        $this->sendHeaders();
        $this->finishResponse();
        $this->setSent();
    }

    /**
     * {@inheritdoc}
     */
    public function sendHeaders()
    {
        if (headers_sent()) {
            return;
        }

        $this->setStatusHeader();
        $this->headers->sendAll(false, $this->getStatusCode());

        foreach ($this->headers->getCookies() as $c) {
            setcookie(
                $c->getName(),
                $c->getValue(),
                $c->getExpireTime(),
                $c->getPath(),
                $c->getDomain(),
                $c->isSecure(),
                $c->isHttpOnly()
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sendContent()
    {
        echo $this->content;

        $this->content = null;
    }

    /**
     * {@inheritdoc}
     */
    public function setProtocolVersion($version)
    {
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function getProtocolVersion()
    {
        return $this->version;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($code, $reasonPhrase = null)
    {
        $code = $this->validateStatus($code);

        $this->status = $code = (int)$code;
        $this->reason = $reasonPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getReasonPhrase()
    {
        if (null === $this->reason) {
            return Reason::phrase($this->status) ?: 'Unknowen';
        }

        return $this->reason;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeader($header, $value)
    {
        $this->headers->set($header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addHeader($header, $value)
    {
        $this->headers->add($header, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function removeHeader($header)
    {
        $this->headers->remove($header);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaders()
    {
        return $this->headers->all();
    }

    /**
     * {@inheritdoc}
     */
    public function hasHeader($header)
    {
        return $this->headers->has($header);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader($header)
    {
        return implode(',', $this->headers->get($header, []));
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderAsArray($header)
    {
        return $this->headers->get($header, []);
    }

    /**
     * {@inheritdoc}
     */
    public function setBody(StreamableInterface $body)
    {
        $this->body = $body;
    }

    /**
     * {@inheritdoc}
     */
    public function getBody()
    {
        if (null === $this->body) {
            $this->prepare();
            $this->body = Body::createFromString($this->contents);
        }

        return $this->body;
    }

    /**
     * ok
     *
     * @return bool
     */
    public function isOk()
    {
        return !$this->isStatusUnknowen() && $this->status < self::STATUS_BAD_REQUEST;
    }

    /**
     * isInfo
     *
     * @return bool
     */
    public function isInfo()
    {
        return self::STATUS_OK > $this->status;
    }

    /**
     * error
     *
     * @return bool
     */
    public function isError()
    {
        return $this->status >= self::STATUS_BAD_REQUEST;
    }

    /**
     * {@inheritdoc}
     */
    public function isServerError()
    {
        return $this->isError() && self::STATUS_ERROR >= $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function isClientError()
    {
        return $this->isError() && self::STATUS_ERROR < $this->status;
    }

    /**
     * isRedirect
     *
     * @return void
     */
    public function isRedirect()
    {
        return self::STATUS_MULTI_CHOICE >= $this->status && self::STATUS_BAD_REQUEST < $this->status;
    }

    /**
     * isNotFound
     *
     * @return void
     */
    public function isNotFound()
    {
        return self::STATUS_NOT_FOUND === $this->status;
    }

    /**
     * Validates a http status code.
     *
     * @param string|int $status http status code
     *
     * @throws InvalidArgumentException
     * @return boolean
     */
    protected function validateStatus($status)
    {
        //  validate against none RCF codes
        if (null !== Reason::phrase($status = (int)$status)) {
            return $status;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Invalid http status or none RCF status code "%s".',
                is_scalar($status) ? (string)$status : gettype($status)
            )
        );
    }

    /**
     * prepare
     *
     * @return void
     */
    protected function prepare()
    {
        if ($this->body instanceof StreamableInterface) {
            $content = $this->body->getContents();
        } else {
            $content = $this->contents;
            if (is_array($content)) {
                $content = json_encode($content);
                $this->headers->set('Content-type', 'application/json');
            } elseif (null === $content) {
                $content = '';
            }
        }

        $this->contents = $content;
    }

    /**
     * setStatusHeader
     *
     * @return void
     */
    protected function setStatusHeader()
    {
        $value = sprintf('http/%s: %d %s', $this->version, $this->status, $this->getReasonPhrase());

        header($value, true, $this->getStatusCode());
    }

    /**
     * setSent
     *
     * @return void
     */
    protected function setSent()
    {
        $this->sent = true;
    }

    /**
     * responseSent
     *
     * @return void
     */
    protected function responseSent()
    {
        return (bool)$this->sent;
    }

    /**
     * prepareContent
     *
     * @return string
     */
    protected function prepareContent()
    {
        return $this->contents;
    }

    /**
     * finishResponse
     *
     * @return void
     */
    protected function finishResponse()
    {
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } elseif ('cli' !== PHP_SAPI) {
            $this->finishCliResponse();
        }

        flush();
    }

    /**
     * finishCliResponse
     *
     * @return void
     */
    protected function finishCliResponse()
    {
        $status = ob_get_status(true);
        $plevel = null;
        // return immediately if previous level is the same as current
        // level
        while (($level = ob_get_level()) > 0 && $plevel !== $level) {
            $plevel = $level;
            if ($this->outputBufferIsRemovable($status[$level - 1])) {
                ob_end_flush();
            }
        }
    }

    /**
     * Checks If the output buffer created by ob_start can be removed
     * without exiting the script
     *
     * @param mixed|array $status
     *
     * @return boolen
     */
    private function outputBufferIsRemovable($status = null)
    {
        return $status && isset($status['flags']) && ($status['flags'] & PHP_OUTPUT_HANDLER_REMOVABLE);
    }
}
