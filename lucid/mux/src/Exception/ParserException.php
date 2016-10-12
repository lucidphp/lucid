<?php

/*
 * This File is part of the Lucid\Mux\Exception package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Exception;

use LogicException;

/**
 * @class ParserException
 *
 * @package Lucid\Mux\Exception
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class ParserException extends LogicException
{
    /**
     * @param string $var
     * @return ParserException
     */
    public static function nestedOptional($var) : ParserException
    {
        return new self(sprintf('Nested optional variable {%s?} has no default value.', $var));
    }
}
