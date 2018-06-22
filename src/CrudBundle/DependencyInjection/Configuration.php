<?php

namespace Siwymilek\CrudBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('core_crud');

        $rootNode
            ->children()
                // Resources
                ->arrayNode('resources')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('model')->defaultNull()->end()
                            ->arrayNode('repository')
                                ->addDefaultsIfNotSet()
                                ->treatNullLike([])
                                ->children()
                                    ->scalarNode('class')->defaultNull()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                // Grids
                ->arrayNode('grids')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('alias')->defaultNull()->end()
                            ->scalarNode('path')->defaultNull()->end()
                            ->scalarNode('template')->defaultNull()->end()
                            ->scalarNode('resource')->defaultNull()->end()
                            ->arrayNode('except')
                                ->scalarPrototype()->end()
                            ->end()
                            ->arrayNode('types')
                                ->children()
                                    ->arrayNode('list')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('repository_method')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifString()
                                                ->then(function($value) {
                                                    return [
                                                        'name' => $value,
                                                        'arguments' => []
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('name')->defaultValue('findAllQueryBuilder')->end()
                                                    ->arrayNode('arguments')
                                                       ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('security')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->arrayNode('voters')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('pagination')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifTrue(function ($v) {
                                                    return $v === false;
                                                })
                                                ->then(function($value) {
                                                    return [
                                                        'enabled' => $value
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->booleanNode('enabled')->defaultTrue()->end()
                                                    ->scalarNode('key')->defaultValue('page')->end()
                                                    ->scalarNode('limit')->defaultValue(10)->end()
                                                    ->arrayNode('sorting')
                                                        ->addDefaultsIfNotSet()
                                                        ->children()
                                                            ->scalarNode('sort')->defaultValue('id')->end()
                                                            ->scalarNode('order')->defaultValue('DESC')->end()
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->arrayNode('serialization')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('method')->defaultValue('json')->end()
                                                    ->arrayNode('groups')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                        ->end()
                                    ->end()

                                    ->arrayNode('show')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('repository_method')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifString()
                                                ->then(function($value) {
                                                    return [
                                                        'name' => $value,
                                                        'arguments' => []
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('name')->defaultValue('find')->end()
                                                    ->arrayNode('arguments')
                                                        ->defaultValue(['id' => "expr:service('request_stack').getCurrentRequest().get('id', [])"])
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('security')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->arrayNode('voters')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->arrayNode('serialization')
                                            ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('method')->defaultValue('json')->end()
                                                    ->arrayNode('groups')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                        ->end()
                                    ->end()

                                    ->arrayNode('create')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('security')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->arrayNode('voters')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->scalarNode('form')->defaultNull()->end()

                                            ->arrayNode('serialization')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('method')->defaultValue('json')->end()
                                                    ->arrayNode('groups')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->arrayNode('redirect')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifString()
                                                ->then(function($value) {
                                                    return [
                                                        'type' => 'default',
                                                        'value' => $value
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('type')->defaultNull()->end()
                                                    ->scalarNode('value')->defaultNull()->end()
                                                    ->arrayNode('arguments')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                    ->arrayNode('update')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('repository_method')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifString()
                                                ->then(function($value) {
                                                    return [
                                                        'name' => $value,
                                                        'arguments' => []
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('name')->defaultValue('find')->end()
                                                        ->arrayNode('arguments')
                                                            ->defaultValue(['id' => "expr:service('request_stack').getCurrentRequest().get('id', [])"])
                                                            ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('security')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->arrayNode('voters')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->scalarNode('form')->defaultNull()->end()

                                            ->arrayNode('serialization')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('method')->defaultValue('json')->end()
                                                    ->arrayNode('groups')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->arrayNode('redirect')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifString()
                                                ->then(function($value) {
                                                    return [
                                                        'type' => 'default',
                                                        'value' => $value
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('type')->defaultNull()->end()
                                                    ->scalarNode('value')->defaultNull()->end()
                                                    ->arrayNode('arguments')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()

                                    ->arrayNode('delete')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('security')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->arrayNode('voters')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                            ->arrayNode('serialization')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('method')->defaultValue('json')->end()
                                                    ->arrayNode('groups')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()

                                            ->arrayNode('redirect')
                                                ->addDefaultsIfNotSet()
                                                ->beforeNormalization()
                                                ->ifString()
                                                ->then(function($value) {
                                                    return [
                                                        'type' => 'default',
                                                        'value' => $value
                                                    ];
                                                })
                                                ->end()
                                                ->children()
                                                    ->scalarNode('type')->defaultNull()->end()
                                                    ->scalarNode('value')->defaultNull()->end()
                                                    ->arrayNode('arguments')
                                                        ->scalarPrototype()->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
