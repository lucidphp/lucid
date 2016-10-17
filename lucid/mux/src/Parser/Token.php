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

    /** @var TokenInterface */
    public $prev;

    /** @var TokenInterface */
    public $next;

    /**
     * Token constructor.
     *
     * @param mixed               $value
     * @param TokenInterface|null $prev
     * @param TokenInterface|null $next
     */
    public function __construct(string $value, TokenInterface $prev = null, TokenInterface $next = null)
    {
        $this->value = $value;
        $this->prev = $prev;
        $this->next = $next;
    }

    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->value;
    }

    /**
     * {@inheritdoc}
     */
    public function next() : ?TokenInterface
    {
        return $this->next;
    }

    /**
     * {@inheritdoc}
     */
    public function prev() : ?TokenInterface
    {
        return $this->prev;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        return preg_quote($this->value, ParserInterface::EXP_DELIM);
    }
}
