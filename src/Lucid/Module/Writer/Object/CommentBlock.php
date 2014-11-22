<?php

/*
 * This File is part of the Lucid\Module\Writer package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Writer\Object;

use Lucid\Module\Writer\Writer;

/**
 * @class CommentBlock
 * @see DocBlock
 *
 * @package Lucid\Module\Writer
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class CommentBlock extends DocBlock
{
    protected function openBlock(Writer $writer)
    {
        return $writer->writeln('/*');
    }
}
