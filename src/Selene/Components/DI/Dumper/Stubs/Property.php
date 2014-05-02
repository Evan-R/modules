<?php

/**
 * This File is part of the Selene\Components\DI\Dumper\Stubs package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Stubs;

/**
 * @class Property
 * @package Selene\Components\DI\Dumper\Stubs
 * @version $Id$
 */
class Property extends Stub
{
    /**
     * @param mixed $name
     * @param string $visibility
     * @param mixed $type
     *
     * @access public
     * @return mixed
     */
    public function __construct($name, $visibility = 'public', $defaultValue = null, $type = null, $static = false)
    {
        $this->name = $name;
        $this->value = $defaultValue;
        $this->visibility = in_array($visibility, ['public', 'protected', 'private']) ? $visibility : 'public';
        $this->type = $type;
        $this->static = (bool)$static;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $indent = $this->indent(4);
        $static = $this->static ? ' static' : null;
        $comment = $this->dumpComment();

        return sprintf('%s%s%s%s%s $%s;%s', $comment, PHP_EOL, $indent, $this->visibility, $static, $this->name, PHP_EOL);
    }

    protected function dumpComment()
    {
        $comment = new DocComment($this->name, null, ['return' => $this->type]);

        return $comment->dump();
    }
}
