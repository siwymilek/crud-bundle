<?php

namespace Siwymilek\CrudBundle\Handler;
use Siwymilek\CrudBundle\Grid\Grid;
use Siwymilek\CrudBundle\Grid\GridInterface;

/**
 * Class GridHandler
 * @package Siwymilek\CrudBundle\Handler
 */
class GridHandler implements GridHandlerInterface {

    /**
     * @var GridInterface[]
     */
    private $gridsCollection = [];

    /**
     * @var ResourceHandlerInterface
     */
    private $resourceHandler;

    /**
     * GridHandler constructor.
     * @param array $config
     * @param ResourceHandlerInterface $resourceHandler
     * @throws \ReflectionException
     */
    public function __construct($config = [], ResourceHandlerInterface $resourceHandler)
    {
        $this->resourceHandler = $resourceHandler;

        foreach($config as $name => $grid) {
            $this->gridsCollection[$name] = new Grid($grid, $name, $this->resourceHandler);
        }
    }

    /**
     * @return GridInterface[]
     */
    public function getNodes() {
        return $this->gridsCollection;
    }

    /**
     * @param $name
     * @return GridInterface
     */
    public function getGrid($name) {

        return $this->gridsCollection[$name];
    }
}