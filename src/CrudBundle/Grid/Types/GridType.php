<?php

namespace Siwymilek\CrudBundle\Grid\Types;
use Doctrine\Common\Util\Inflector;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Class GridType
 * @package Siwymilek\CrudBundle\Grid\Types
 */
class GridType implements GridTypeInterface, GridListTypeInterface, GridShowTypeInterface, GridCreateTypeInterface, GridUpdateTypeInterface, GridDeleteTypeInterface {

    /**
     * @var string
     */
    private $type = '';

    /**
     * @var array
     */
    private $security = [];

    /**
     * @var array
     */
    private $serialization = [];

    /**
     * @var array
     */
    private $pagination = [];

    /**
     * @var array
     */
    private $redirect = [];

    /**
     * @var array
     */
    private $repositoryMethod = [];

    /**
     * @var string
     */
    private $form;

    /**
     * GridType constructor.
     * @param $typeOfType
     * @param array $config
     * @throws \ReflectionException
     */
    public function __construct($typeOfType, $config = [])
    {
        $this->type = $typeOfType;

        $reflection = new \ReflectionClass($this);
        foreach($reflection->getProperties() as $property) {
            $propertyName = Inflector::tableize($property->name);

            if(!in_array($propertyName, ['type', 'form']) && isset($config[$propertyName])) {
                $this->{Inflector::camelize($property->name)} = $config[$propertyName];
            }

            if($propertyName === 'form' && isset($config[$propertyName])) {
                $this->form = $config[$propertyName];
            }
        }
    }

    /**
     * @return array
     */
    public function getSecurity()
    {
        return $this->security;
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return array
     */
    public function getRedirect()
    {
        return $this->redirect;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getSerialization()
    {
        return $this->serialization;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getRepositoryMethod()
    {
        return $this->repositoryMethod;
    }

    /**
     * @return mixed|string
     */
    public function getForm()
    {
        return $this->form;
    }
}