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
 * @class ImportHelper
 * @package Selene\Module\Writer\Object
 * @version $Id$
 */
trait ImportHelper
{
    /**
     * importResolver
     *
     * @var ImportResolver
     */
    private $importResolver;

    /**
     * getImportResolver
     *
     * @return ImportResolver
     */
    public function getImportResolver()
    {
        if (null === $this->importResolver) {
            $this->importResolver = new ImportResolver;
        }

        return $this->importResolver;
    }

    /**
     * addToImportPool
     *
     * @param array $pool
     * @param string $string
     *
     * @return void
     */
    protected function addToImportPool(array &$pool, $string)
    {
        $this->getImportResolver()->add($string);
        $pool[] = $string;
    }
}
