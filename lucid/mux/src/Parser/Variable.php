<?php declare(strict_types=1);

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
 * @class Variable
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Variable extends Token implements VariableInterface
{
    /** @var string */
    private $regex;

    /** @var bool */
    private $required;

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
     * @param string $default
     */
    public function __construct(
        string $name,
        bool $required = true,
        string $constr = null,
        TokenInterface $prev = null,
        TokenInterface $next = null,
        string $default = '/'
    ) {
        $this->required   = $required;
        $this->constraint = $constr;
        $this->default    = $default;

        parent::__construct($name, $prev, $next);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString() : string
    {
        return $this->getRegex();
    }

    /**
     * {@inheritdoc}
     */
    public function isRequired() : bool
    {
        return $this->required;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegex() : string
    {
        if (null === $this->constraint) {
            $this->constraint = sprintf(
                '[^%s%s]++',
                preg_quote($this->default, ParserInterface::EXP_DELIM),
                $this->next instanceof Delimiter ? (string)$this->next : ''
            );
        }

        return $this->regex = sprintf('(?P<%s>%s)', $this->value, $this->constraint);
    }
}
