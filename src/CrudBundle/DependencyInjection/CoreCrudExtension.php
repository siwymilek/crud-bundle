<?php

namespace Siwymilek\CrudBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CoreCrudExtension extends Extension {

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $resourcesConfig = $config['resources'];
        $gridsConfig = $config['grids'];

        $resourceHandlerDefinition = $container->getDefinition('core.crud_bundle.handler.resource_handler');
        $resourceHandlerDefinition->replaceArgument(0, $resourcesConfig);

        $gridHandlerDefinition = $container->getDefinition('core.crud_bundle.handler.grid');
        $gridHandlerDefinition->replaceArgument(0, $gridsConfig);
    }
}