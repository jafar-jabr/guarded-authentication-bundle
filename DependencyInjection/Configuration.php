<?php

namespace Jafar\Bundle\GuardedAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jafar_guarded_authentication');
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('pass_phrase')
            ->end()
            ->scalarNode('token_ttl')
            ->defaultValue(3600)
            ->end()
            ->scalarNode('login_form')
            ->end()
            ->scalarNode('login_route')
            ->end()
            ->scalarNode('home_page_route')
            ->end()
            ->scalarNode('api_login_route')
            ->end()
            ->scalarNode('api_home_page_route')
            ->end();
        return $treeBuilder;
    }
}
