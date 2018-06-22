<?php

namespace Siwymilek\CrudBundle\Repository;

use Doctrine\Common\Util\Inflector;

/**
 * Class Repository
 * @package Siwymilek\CrudBundle\Repository
 */
abstract class Repository extends \Doctrine\ORM\EntityRepository implements RepositoryInterface {

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function findAllQueryBuilder()
    {
        return $this->createQueryBuilder($this->getAlias());
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        $calledClassName = get_called_class();
        $calledClassName = explode('\\', $calledClassName);
        $calledClassName = end($calledClassName);
        $calledClassName = str_replace('Repository', null, $calledClassName);

        return Inflector::camelize($calledClassName);
    }
}