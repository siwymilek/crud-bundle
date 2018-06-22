<?php

namespace Siwymilek\CrudBundle\Handler;

/**
 * Interface GridHandlerInterface
 * @package Siwymilek\CrudBundle\Handler
 */
interface GridHandlerInterface {
    public function __construct($config = [], ResourceHandlerInterface $resourceHandler);

    public function getNodes();
}