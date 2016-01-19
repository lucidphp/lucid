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

    /**
     * Constructor.
     *
     * @param string $name       the variable name
     * @param bool   $required   the variable is requiered
     * @param string $constraint a regex constraint.
     * @param TI     $prev       the previous token
     * @param TI     $next       the next token
     */
    public function __construct($name, $required = true, $constraint = null, TI $prev = null, TI $next = null)
    {
        $this->required = $required;
        $this->constraint = $constraint;

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
            $this->constraint = sprintf('[^%s%s]++', preg_quote('/', ParserInterface::EXP_DELIM), $delim);
        }

        return $this->regex = sprintf('(?P<%s>%s)', $this->value, $this->constraint);
    }
}
