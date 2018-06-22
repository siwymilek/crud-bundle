<?php

namespace Siwymilek\CrudBundle\Grid;

use Siwymilek\CrudBundle\Grid\Types\GridType;
use Siwymilek\CrudBundle\Handler\ResourceHandlerInterface;
use Siwymilek\CrudBundle\Handler\ResourceInterface;

interface GridInterface {
    /**
     * GridInterface constructor.
     * @param array $config
     * @param $name
     * @param ResourceHandlerInterface $resourceHandler
     */
    public function __construct($config = [], $name, ResourceHandlerInterface $resourceHandler);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return string
     */
    public function getTemplate();

    /**
     * @return array
     */
    public function getExcept();

    /**
     * @return ResourceInterface
     */
    public function getResource();

    /**
     * @return GridType[]
     */
    public function getTypes();
}