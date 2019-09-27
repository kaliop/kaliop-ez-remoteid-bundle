<?php

namespace Kaliop\EzRemoteIdBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    const DEFAULT_PATTERN = '/^[a-z0-9]+$/';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kaliop_ez_remote_id');

        $rootNode
            ->children()
                ->arrayNode('default')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('pattern')->defaultValue(self::DEFAULT_PATTERN)->end()
                        ->scalarNode('max_length')->defaultValue(32)->end()
                    ->end()
                ->end()
                ->arrayNode('content_types')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                           ->scalarNode('pattern')->defaultValue(self::DEFAULT_PATTERN)->end()
                           ->scalarNode('max_length')->defaultValue(32)->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
