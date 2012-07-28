<?php

/*
 * This file is part of the FOSGoogleBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\GoogleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Lukas Kahwe Smith <smith@pooteeweet.org>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_google');

        $rootNode
            ->fixXmlConfig('permission', 'permissions')
            ->children()
                ->scalarNode('app_name')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('client_id')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('client_secret')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('redirect_uri')->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('scopes')->prototype('scalar')->isRequired()->end()->end()
                ->scalarNode('state')->defaultValue('auth')->end()
                ->scalarNode('access_type')->defaultValue('online')->end()
                ->scalarNode('approval_prompt')->defaultValue('auto')->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('api')->defaultValue('FOS\GoogleBundle\Google\GoogleSessionPersistence')->end()
                        ->scalarNode('helper')->defaultValue('FOS\GoogleBundle\Templating\Helper\GoogleHelper')->end()
                        ->scalarNode('twig')->defaultValue('FOS\GoogleBundle\Twig\Extension\GoogleExtension')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
