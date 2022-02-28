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
 * Class Configuration.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jafar_guarded_authentication');
        $rootNode    = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->scalarNode('pass_phrase')->end()
            ->scalarNode('token_ttl')->end()
            ->scalarNode('login_route')->end()
            ->scalarNode('refresh_ttl')->end()
            ->end();

        return $treeBuilder;
    }
}
