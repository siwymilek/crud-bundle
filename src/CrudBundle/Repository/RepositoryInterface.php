<?php

namespace Siwymilek\CrudBundle\Repository;

use Doctrine\ORM\QueryBuilder;

/**
 * Interface RepositoryInterface
 * @package Siwymilek\CrudBundle\Repository
 */
interface RepositoryInterface {

    /**
     * @return QueryBuilder
     */
    public function findAllQueryBuilder();

    /**
     * @return string
     */
    public function getAlias();
}