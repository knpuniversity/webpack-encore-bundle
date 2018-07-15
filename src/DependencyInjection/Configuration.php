<?php

namespace KnpUniversity\WebpackEncoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('webpack_encore');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('webpack_encore');

        $rootNode
            ->children()
                ->scalarNode('entrypoints_json_path')
                    ->isRequired()
                    ->info('The path to the entrypoints.json emitted by Webpack Encore')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}