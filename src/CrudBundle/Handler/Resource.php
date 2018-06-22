<?php

namespace Siwymilek\CrudBundle\Handler;

use Siwymilek\CrudBundle\Model\ModelInterface;
use Siwymilek\CrudBundle\Repository\RepositoryInterface;

/**
 * Class Resource
 * @package Siwymilek\CrudBundle\Handler
 */
class Resource implements ResourceInterface {

    /**
     * @var ModelInterface
     */
    private $model;

    /**
     * @var ResourceInterface
     */
    private $repository;

    /**
     * @var string
     */
    private $modelFQDN;

    /**
     * @var string
     */
    private $name;

    /**
     * Resource constructor.
     * @param $name
     * @param $modelFQDN
     * @param ModelInterface $model
     * @param RepositoryInterface $repository
     */
    public function __construct($name, $modelFQDN, ModelInterface $model, RepositoryInterface $repository)
    {
        $this->model = $model;
        $this->name = $name;
        $this->modelFQDN = $modelFQDN;
        $this->repository = $repository;
    }

    /**
     * @return ModelInterface
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getModelFQDN()
    {
        return $this->modelFQDN;
    }

    /**
     * @return ResourceInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}