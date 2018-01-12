<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class Configuration
 * @package Jafar\Bundle\GuardedAuthenticationBundle\DependencyInjection
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
            ->defaultValue('')
            ->end()
            ->scalarNode('token_ttl')
            ->defaultValue(3600)
            ->end()
            ->scalarNode('login_route')
            ->defaultValue('')
            ->end()
            ->scalarNode('home_page_route')
            ->defaultValue('')
            ->end()
            ->scalarNode('api_login_route')
            ->defaultValue('')
            ->end()
            ->scalarNode('api_home_page_route')
            ->defaultValue('')
            ->end();

        return $treeBuilder;
    }
}
