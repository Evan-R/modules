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

/**
 * @class DelegateConfigDumper
 * @package Selene\Components\Package
 * @version $Id$
 */
class DelegateConfigDumper implements ConfigDumperInterface, DelegateAbleDumperInterface
{
    private $dumpers;
    private $current;

    public function __construct(array $dumpers = [])
    {
        $this->setDumpers($dumpers);
    }

    /**
     * {@inheritdoc}
     */
    public function getDumper($format)
    {
        if ($this->current && $this->current->supports($format)) {
            return $this->current;
        }

        foreach ($this->dumpers as $dumper) {

            if ($dumper->supports($format)) {
                $this->current = $dumper;

                return $this->current;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($format)
    {
        if (null === $this->getDumper($format)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function dump($name, array $contents = [], $format = null)
    {
        if ($dumper = $this->getDumper($format)) {
            return $dumper->dump($name, $contents, $format);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename()
    {
        if ($dumper === $this->current) {
            return $dumper->getFilename();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDumpers(array $dumpers)
    {
        $this->dumpers = [];

        foreach ($dumpers as $dumper) {
            $this->addDumper($dumper);
        }
    }

    public function addDumper(ConfigDumperInterface $dumper)
    {
        $this->dumpers[] = $dumper;
    }

    /**
     * {@inheritdoc}
     */
    public function dumper(ConfigDumperInterface $dumper)
    {
        $this->dumpers[] = $dumper;
    }
}
