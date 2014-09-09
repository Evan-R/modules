<?php

/*
 * This File is part of the Selene\Module\Writer\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Object;

/**
 * @class CommentBlock
 * @package Selene\Module\Writer\Object
 * @version $Id$
 */
class CommentBlock extends DocBlock
{
    protected function openBlock(Writer $writer)
    {
        return $writer->writeln('/*');
    }
}
