<?php

namespace Siwymilek\CrudBundle\Routing;

use Siwymilek\CrudBundle\Grid\GridInterface;
use Siwymilek\CrudBundle\Grid\Types\GridTypeInterface;
use Siwymilek\CrudBundle\Handler\GridHandlerInterface;
use Symfony\Component\Routing\Route;

/**
 * Class RoutesExtractor
 * @package Siwymilek\CrudBundle\Routing
 */
class RoutesExtractor implements RoutesExtractorInterface {

    const DEFAULT_CONTROLLER_LIST   = 'CoreCrudBundle:Resource:list';
    const DEFAULT_CONTROLLER_SHOW   = 'CoreCrudBundle:Resource:show';
    const DEFAULT_CONTROLLER_CREATE = 'CoreCrudBundle:Resource:create';
    const DEFAULT_CONTROLLER_UPDATE = 'CoreCrudBundle:Resource:update';
    const DEFAULT_CONTROLLER_DELETE = 'CoreCrudBundle:Resource:delete';

    /**
     * @var Route[]
     */
    private $routes = [];

    /**
     * RoutesExtractor constructor.
     * @param GridHandlerInterface $gridHandler
     */
    public function __construct(GridHandlerInterface $gridHandler)
    {
        foreach($gridHandler->getNodes() as $grid) {
            $this->preprocessGrid($grid);
        }
    }

    /**
     * @param GridInterface $grid
     */
    private function preprocessGrid(GridInterface $grid) {
        foreach($grid->getTypes() as $gridType) {
            if(!in_array($gridType->getType(), $grid->getExcept())) {
                $details = $this->getRouteDetails($gridType, $grid->getPath());

                foreach($details as $detail) {
                    $this->routes[$grid->getName().'.'.$detail['routeName']] = new Route(
                        $detail['path'],
                        [
                            '_controller' => $detail['_controller'],
                            '_gridName' => $grid->getName()
                        ],
                        $detail['requirements'],
                        [
                            'expose' => true
                        ], // options
                        '', // host
                        [], // schemes,
                        $detail['methods'] // wtf is this last argument?
                    );
                }
            }
        }
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param GridTypeInterface $gridType
     * @param $path
     * @return array
     */
    private function getRouteDetails(GridTypeInterface $gridType, $path)
    {
        $details = [];

        if($gridType->getType() === 'list') {
            $details = [
                [
                    'path' => $path,
                    'methods' => ['GET'],
                    'requirements' => [],
                    '_controller' => static::DEFAULT_CONTROLLER_LIST,
                    'routeName' => $gridType->getType().'_view'
                ]
            ];
        }

        if($gridType->getType() === 'show') {
            $details = [
                [
                    'path' => $path.'/{id}',
                    'methods' => ['GET'],
                    'requirements' => ['id' => '\d+'],
                    '_controller' => static::DEFAULT_CONTROLLER_SHOW,
                    'routeName' => $gridType->getType().'_view'
                ]
            ];
        }

        if($gridType->getType() === 'create') {
            $details = [
                [
                    'path' => $path.'/create',
                    'methods' => ['GET'],
                    'requirements' => [],
                    '_controller' => static::DEFAULT_CONTROLLER_CREATE,
                    'routeName' => $gridType->getType().'_view'
                ],
                [
                    'path' => $path.'/create',
                    'methods' => ['POST'],
                    'requirements' => [],
                    '_controller' => static::DEFAULT_CONTROLLER_CREATE,
                    'routeName' => $gridType->getType()
                ]
            ];
        }

        if($gridType->getType() === 'update') {
            $details = [
                [
                    'path' => $path.'/{id}/edit',
                    'methods' => ['GET'],
                    'requirements' => ['id' => '\d+'],
                    '_controller' => static::DEFAULT_CONTROLLER_UPDATE,
                    'routeName' => $gridType->getType().'_view'
                ],
                [
                    'path' => $path.'/{id}',
                    'methods' => ['PATCH', 'PUT'],
                    'requirements' => ['id' => '\d+'],
                    '_controller' => static::DEFAULT_CONTROLLER_UPDATE,
                    'routeName' => $gridType->getType()
                ]
            ];
        }

        if($gridType->getType() === 'delete') {
            $details = [
                [
                    'path' => $path.'/{id}',
                    'methods' => ['DELETE'],
                    'requirements' => ['id' => '\d+'],
                    '_controller' => static::DEFAULT_CONTROLLER_DELETE,
                    'routeName' => $gridType->getType()
                ]
            ];
        }

        return $details;
    }
}