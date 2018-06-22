<?php

namespace Siwymilek\CrudBundle\Routing;

use Siwymilek\CrudBundle\Handler\GridHandlerInterface;
use Symfony\Component\Routing\Route;

/**
 * Interface RoutesExtractorInterface
 * @package Siwymilek\CrudBundle\Routing
 */
interface RoutesExtractorInterface {

    /**
     * RoutesExtractorInterface constructor.
     * @param GridHandlerInterface $gridHandler
     */
    public function __construct(GridHandlerInterface $gridHandler);

    /**
     * @return Route[]
     */
    public function getRoutes();
}