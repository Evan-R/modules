<?php

/**
 * This File is part of the Selene\Components\DI package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\DI\Dumper\Stubs;

/**
 * @class ClassHeader extends Stub
 * @see Stub
 *
 * @package Selene\Components\DI
 * @version $Id$
 * @author Thomas Appel <mail@thomas-appel.com>
 * @license MIT
 */
class ClassHeader extends Stub
{
    public function __construct($className, $baseClass, NamespaceStatement $ns, UseStatements $uses)
    {
        $this->className = $className;
        $this->baseClass = $baseClass;
        $this->namespace = $ns;
        $this->uses = $uses;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        return $this->getClassDoc($this->namespace->dump(), $this->uses->dump(), $this->className, $this->baseClass);
    }

    /**
     * getClassDoc
     *
     * @param string $namespace
     * @param string $uses
     * @param string $className
     * @param string $baseClass
     *
     * @access private
     * @return string
     */
    private function getClassDoc($namespace, $uses, $className, $baseClass)
    {
        $time = date('D m Y, H:i:s');
        return <<<EOL
<?php

/**
 * This File is part of the Selene project
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

$namespace

$uses
/**
 * @class $className
 *
 * This file was autogenerated on $time;
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 */
class $className extends $baseClass
{
    use Getter;
EOL;
    }
}
