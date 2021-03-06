<?php

/*
 * This File is part of the Selene\Module\Writer\Generator\Object package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Module\Writer\Object;

use \Selene\Module\Writer\Writer;


/**
 * @class IMethod
 * @package Selene\Module\Writer\Generator\Object
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 */
class InterfaceMethod extends Method
{
    /**
     * Constructor.
     *
     * @param string $name
     * @param string $type
     * @param boolean $static
     */
    public function __construct($name, $type = self::T_VOID, $static = false)
    {
        parent::__construct($name, self::IS_PUBLIC, $type, $static);
    }

    /**
     * {@inheritdoc}
     */
    public function generate($raw = false)
    {
        $writer = new Writer(4, true);
        $writer->setOutputIndentation(1);

        $this->getMethodDeclaration($writer, false);
        $writer->appendln(';');

        return $raw ? $writer : $writer->dump();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException every time it is called.
     */
    public function setAbstract($abstract)
    {
        if ((bool)$abstract) {
            throw new \LogicException('Cannot set interface method abstract.');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException every time it is called.
     */
    public function setBody($body)
    {
        throw new \LogicException('Cannot set a method body on an interface method.');
    }
}
