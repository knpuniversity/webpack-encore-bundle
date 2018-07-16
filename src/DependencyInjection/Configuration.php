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
                ->scalarNode('output_path')
                    ->isRequired()
                    ->info('The path where Encore is building the assets - i.e. Encore.setOutputPath()')
                ->end()
                ->scalarNode('asset_path_prefix')
                    ->isRequired()
                    ->info('The public prefix to your assets that you normally use with the asset() function (e.g. build/) - should match the "setManifestKeyPrefix()" value in webpack.config.js, if set.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}