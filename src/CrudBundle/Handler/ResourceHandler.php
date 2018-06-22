<?php

namespace Siwymilek\CrudBundle\Handler;

use Doctrine\ORM\EntityManagerInterface;
use http\Exception\RuntimeException;

/**
 * Class ResourceHandler
 * @package Siwymilek\CrudBundle\Handler
 */
class ResourceHandler implements ResourceHandlerInterface {

    protected $config = [];
    protected $em;

    /**
     * ResourceHandler constructor.
     * @param array $config
     * @param EntityManagerInterface $entityManager
     */
    public function __construct($config = [], EntityManagerInterface $entityManager)
    {
        $this->config = $config;
        $this->em = $entityManager;
    }

    /**
     * @param $resourceName
     * @return ResourceInterface
     */
    public function getResource($resourceName)
    {
        if(!isset($this->config[$resourceName])) {
            throw new RuntimeException('Resource `'.$resourceName.'` does not exist.');
        }

        $resource = $this->config[$resourceName];

        $model = isset($resource['model']) ? new $resource['model'] : null;
        $repository = isset($resource['repository']['class']) ? $this->em->getRepository($resource['repository']['class']) : null;

        return new Resource($resourceName, $resource['model'], $model, $repository);
    }
}