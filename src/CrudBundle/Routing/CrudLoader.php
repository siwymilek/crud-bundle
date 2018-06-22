<?php

namespace Siwymilek\CrudBundle\Routing;

use Siwymilek\CrudBundle\Handler\GridHandlerInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

class CrudLoader extends Loader {

    /**
     * @var bool
     */
    private $isLoaded = false;

    /**
     * @var RoutesExtractorInterface
     */
    private $routesExtractor;

    /**
     * @var GridHandlerInterface
     */
    private $gridHandler;

    /**
     * CrudLoader constructor.
     * @param GridHandlerInterface $gridHandler
     * @param RoutesExtractorInterface $routesExtractor
     */
    public function __construct(GridHandlerInterface $gridHandler,  RoutesExtractorInterface $routesExtractor)
    {
        $this->gridHandler = $gridHandler;
        $this->routesExtractor = $routesExtractor;
    }

    public function load($resource, $type = null)
    {
        if($this->isLoaded === true) {
            throw new \RuntimeException('Do not run crud loader twice.');
        }

        $routesCollection = new RouteCollection();
        foreach($this->routesExtractor->getRoutes() as $name => $route) {
            $routesCollection->add($name, $route);
        }

        $this->isLoaded = true;
        return $routesCollection;
    }

    public function supports($resource, $type = null)
    {
        return $type === 'crud';
    }
}