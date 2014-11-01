<?php

/*
 * This File is part of the Lucid\Module\Template\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Exception;

/**
 * @class RenderException
 *
 * @package Lucid\Module\Template\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class RenderException extends \RuntimeException
{
    /**
     * Constructor.
     *
     * @param mixed $message
     * @param \Exception $prevException
     * @param int $code
     */
    public function __construct($message, \Exception $prevException, $code = 0)
    {
        parent::__construct($message, $code, $prevException);
    }

    public static function invalidParameter($param)
    {
        return new self(sprintf('Invalid parameter "%s".'));
    }
}
