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

use Lucid\Module\Template\TemplateInterface;

/**
 * @class LoaderException
 *
 * @package Lucid\Module\Template\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class LoaderException extends \InvalidArgumentException implements TemplateExteption
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

    public static function templateNotFound(TemplateInterface $template)
    {
        return new self(sprintf('Template "%s" not found.', $template->getName()));
    }
}
