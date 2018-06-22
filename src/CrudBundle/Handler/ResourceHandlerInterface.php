<?php

namespace Siwymilek\CrudBundle\Handler;

/**
 * Interface ResourceHandlerInterface
 * @package Siwymilek\CrudBundle\Handler
 */
interface ResourceHandlerInterface {

    /**
     * @return Resource
     */
    public function getResource($resourceName);
}