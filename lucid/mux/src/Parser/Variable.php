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

use Lucid\Mux\Parser\TokenInterface as TI;

/**
 * @class Variable
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Variable extends Token
{
    /** @var bool */
    public $required;

    /** @var string */
    public $regex;

    /** @var string */
    private $constraint;

    /** @var string */
    private $default;

    /**
     * Variable constructor.
     * @param string $name
     * @param bool $required
     * @param string $constr
     * @param TokenInterface $prev
     * @param TokenInterface$next
     * @param string $def
     */
    public function __construct(
        $name,
        $required = true,
        $constr = null,
        TokenInterface $prev = null,
        TokenInterface $next = null,
        $def = '/'
    ) {
        $this->required   = $required;
        $this->constraint = $constr;
        $this->default    = $def;

        parent::__construct($name, $prev, $next);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->getRegex();
    }

    /**
     * getRegex
     *
     * @return string
     */
    public function getRegex()
    {
        if (null === $this->constraint) {
            $delim = $this->next instanceof Delimiter ? (string)$this->next : '';
            $this->constraint = sprintf('[^%s%s]++', preg_quote($this->default, ParserInterface::EXP_DELIM), $delim);
        }

        return $this->regex = sprintf('(?P<%s>%s)', $this->value, $this->constraint);
    }
}
