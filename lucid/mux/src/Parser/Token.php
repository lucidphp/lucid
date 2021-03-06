<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Parser;

/**
 * @class Token
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class Token implements TokenInterface
{
    /** @var string */
    public $value;

    /** @var TI */
    public $prev;

    /** @var TI */
    public $next;

    /**
     * __construct
     *
     * @param mixed $value
     * @param TI $prev
     * @param TI $next
     */
    public function __construct($value, TokenInterface $prev = null, TokenInterface $next = null)
    {
        $this->value = $value;
        $this->prev = $prev;
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return preg_quote($this->value, ParserInterface::EXP_DELIM);
    }
}
