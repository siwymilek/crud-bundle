<?php

namespace Siwymilek\CrudBundle\Grid\Types;
use Symfony\Component\Form\FormTypeInterface;

/**
 * Interface GridTypeInterface
 * @package Siwymilek\CrudBundle\Grid\Types
 */
interface GridTypeInterface {

    /**
     * GridTypeInterface constructor.
     * @param $typeOfType
     * @param array $config
     */
    public function __construct($typeOfType, $config = []);

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getSecurity();

    /**
     * @return array
     */
    public function getPagination();

    /**
     * @return array
     */
    public function getSerialization();

    /**
     * @return array
     */
    public function getRedirect();

    /**
     * @return array
     */
    public function getRepositoryMethod();

    /**
     * @return string
     */
    public function getForm();
}