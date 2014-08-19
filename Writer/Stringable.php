<?php

/*
 * This File is part of the Selene\Module\Writer package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer;

/**
 * @class Stringable
 * @package Selene\Module\Writer
 * @version $Id$
 */
trait Stringable
{
    public function __toString()
    {
        if ($this instanceof GeneratorInterface) {
            return $this->generate(GeneratorInterface::RV_STRING);
        }

        if ($this instanceof Writer) {
            return $this->dump();
        }
    }
}
