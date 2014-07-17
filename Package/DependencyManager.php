<?php

/**
 * This File is part of the Selene\Components\Package package
 *
 * (c) Thomas Appel <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Selene\Components\Package;

/**
 * @class DependencyManager
 * @package Selene\Components\Package
 * @version $Id$
 * @author <mail@thomas-appel.com>
 */
class DependencyManager
{
    /**
     * repository
     *
     * @var RepositoryInterface
     */
    private $repository;

    /**
     * current
     *
     * @var array
     */
    private $current;

    /**
     * Constructor.
     *
     * @param PackageRepositoryInterface $repository
     */
    public function __construct(PackageRepositoryInterface $repository)
    {
        $this->current = [];
        $this->repository = $repository;
    }

    /**
     * Get the packages in required order.
     *
     * @return array
     */
    public function getSorted()
    {
        $req = [];

        foreach ($this->repository->all() as $key => $package) {

            foreach ($this->getRequirements($package, true) as $pkg) {

                if (array_key_exists($alias = $pkg->getAlias(), $req)) {
                    continue;
                }

                $req[$alias] = $pkg;
            }
        }

        return $req;
    }

    /**
     * Get dependent packages of a package.
     *
     * If $includeSelf is true, the package will be included as last member.
     *
     * @param PackageInterface $package     the package
     * @param boolean          $includeSelf include the current package observed
     *
     * @throws \InvalidArgumentException if a required package does not exists.
     * @throws \InvalidArgumentException if there's a circular reference.
     *
     * @return array of packages
     */
    public function getRequirements(PackageInterface $package, $includeSelf = false)
    {
        $this->current[$package->getAlias()] = true;

        $req =  $this->doGetRequirements($package);

        unset($this->current[$package->getAlias()]);

        if ($includeSelf) {
            $req[$package->getAlias()] = $package;
        }

        return $req;
    }

    /**
     * Get the package requirements
     *
     * @param PackageInterface $package
     * @param array $res
     * @return array
     */
    protected function doGetRequirements(PackageInterface $package, &$res = [])
    {
        $alias = $package->getAlias();
        $requirements = (array)$package->requires();

        foreach ($requirements as $req) {

            if (!$this->repository->has($req)) {
                $this->handlePackageNotFound($alias, $req);
            }

            if (isset($this->current[$req]) || $req === $alias) {
                $this->handleCircularReference($alias, $req);
            }

            $this->doGetRequirements($this->repository->get($req), $res);

            $res[$req] = $this->repository->get($req);
        }

        return $res;
    }

    /**
     * handleCircularReference
     *
     * @param mixed $package
     * @param mixed $requirement
     *
     * @return void
     */
    private function handleCircularReference($package, $requirement)
    {
        throw new \InvalidArgumentException(
            sprintf(
                'Circular reference error: Package "%1$s" requires "%2$s" wich requires "%1$s".',
                $package,
                $requirement
            )
        );
    }

    /**
     * handlePackageNotFound
     *
     * @param mixed $package
     * @param mixed $requirement
     *
     * @return void
     */
    private function handlePackageNotFound($package, $requirement)
    {
        throw new \InvalidArgumentException(
            sprintf(
                'Package "%1$s" requires "%2$s", but "%2$s" does not exist.',
                $package,
                $requirement
            )
        );
    }
}
