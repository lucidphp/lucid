<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Exception;

/**
 * @class RenderException
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderException extends \RuntimeException implements TemplateException
{
    /**
     * Constructor.
     *
     * @param mixed $message
     * @param \Exception $prevException
     * @param int $code
     */
    public function __construct($message, \Exception $prevException = null, $code = 0)
    {
        parent::__construct($message, $code, $prevException);
    }

    public static function invalidParameter($param)
    {
        return new self(sprintf('Invalid parameter "%s".', $param));
    }
}
