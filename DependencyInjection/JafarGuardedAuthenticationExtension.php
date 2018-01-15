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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class JafarGuardedAuthenticationExtension.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JafarGuardedAuthenticationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $container->setParameter('jafar_guarded_authentication.pass_phrase', $config['pass_phrase'] ?? '');
        $container->setParameter('jafar_guarded_authentication.token_ttl', $config['token_ttl'] ?? 3600);
        $container->setParameter('jafar_guarded_authentication.login_route', $config['login_route'] ?? '');
        $container->setParameter('jafar_guarded_authentication.home_page_route', $config['home_page_route'] ?? '');
        $container->setParameter('jafar_guarded_authentication.api_login_route', $config['api_login_route'] ?? '');
        $container->setParameter(
            'jafar_guarded_authentication.api_home_page_route',
            $config['api_home_page_route'] ?? ''
        );
    }
}
