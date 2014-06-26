<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package\Dumper;

use \Selene\Components\DI\Dumper\Stubs\Lines;
use \Selene\Components\DI\Dumper\Stubs\DocComment;
use \Selene\Components\DI\Dumper\Traits\FormatterTrait;

/**
 * @interface ConfigDumperInterface
 * @package Selene\Components\Package
 * @version $Id$
 */
class PhpConfigDumper implements ConfigDumperInterface
{
    use FormatterTrait;

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        return 'config.php';
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        return 'php' === $format;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($name, array $contents = [], $format = null)
    {
        if (!$this->supports($format)) {
            return;
        }

        $comment = new DocComment(
            'This file was automatically created',
            'Created at ' . (new \DateTime())->format('Y-m-d:H:m:s') . '.',
            [],
            0
        );

        return (new Lines)
            ->add('<?php')
            ->emptyLine()
            ->add($comment)
            ->emptyLine()
            ->add('$builder->addPackageConfig(')
            ->indent()
                ->add("'$name'" . ', ')
                ->add($this->extractParams($contents))
            ->end()
            ->add(');')
            ->end()
        ->dump();
    }
}
