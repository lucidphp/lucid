<?php

/*
 * This File is part of the Lucid\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Writer\Object;

use InvalidArgumentException;

/**
 * @trait VisibilityHelperTrait
 *
 * @package Lucid\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
trait VisibilityHelperTrait
{
    /** @var array */
    private $vpool = [
        MemberInterface::IS_PUBLIC,
        MemberInterface::IS_PROTECTED,
        MemberInterface::IS_PRIVATE
    ];

    /**
     * checkVisibility
     *
     * @param string $visibility
     *
     * @throws \InvalidArgumentException
     * @return bool
     */
    private function checkVisibility($visibility)
    {
        if (in_array($visibility, $this->vpool)) {
            return true;
        }

        throw new InvalidArgumentException(sprintf(
            '"%s" is not a valid visibility, possible values are: %s.',
            $visibility,
            implode(', ', $this->vpool)
        ));
    }
}
