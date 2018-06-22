<?php

namespace Siwymilek\CrudBundle\Grid;

use Siwymilek\CrudBundle\Grid\Types\GridType;
use Siwymilek\CrudBundle\Handler\ResourceHandlerInterface;
use Siwymilek\CrudBundle\Handler\ResourceInterface;

class Grid implements GridInterface {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $alias;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $except = [];

    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * @var array
     */
    private $types = [];

    /**
     * Grid constructor.
     * @param array $config
     * @param ResourceHandlerInterface $resourceHandler
     * @throws \ReflectionException
     */
    public function __construct($config = [], $name, ResourceHandlerInterface $resourceHandler)
    {
        $reflection = new \ReflectionClass($this);
        foreach($reflection->getProperties() as $property) {
            $propertyName = $property->name;

            if(!in_array($propertyName, ['types', 'resource', 'name']) && isset($propertyName)) {
                $this->{$propertyName} = $config[$propertyName];
            }

            if($propertyName === 'resource') {
                $this->resource = $resourceHandler->getResource($config[$propertyName]);
            }

            if($propertyName === 'types') {
                foreach($config[$propertyName] as $typeOfType => $type) {
                    $this->types[] = new GridType($typeOfType, $type);
                }
            }

            if($propertyName === 'name') {
                $this->name = $name;
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function getExcept()
    {
        return $this->except;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getTypes()
    {
        return $this->types;
    }
}