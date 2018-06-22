<?php

namespace Siwymilek\CrudBundle\Handler;

use Siwymilek\CrudBundle\Model\ModelInterface;
use Siwymilek\CrudBundle\Repository\RepositoryInterface;

/**
 * Interface ResourceInterface
 * @package Siwymilek\CrudBundle\Handler
 */
interface ResourceInterface {

    /**
     * @return ModelInterface
     */
    public function getModel();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getModelFQDN();

    /**
     * @return RepositoryInterface
     */
    public function getRepository();
}