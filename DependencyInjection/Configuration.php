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
use Symfony\Component\Config\Definition\Builder\TreeBuilder, Symfony\Component\Config\Definition\ConfigurationInterface;

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

  public function getConfigTreeBuilder( )
  {
    $treeBuilder = new TreeBuilder( );
    $rootNode = $treeBuilder->root( 'fos_google' );

    $rootNode->fixXmlConfig( 'permission', 'permissions' )->children( )// childrens
        ->scalarNode( 'app_name' )->isRequired( )->cannotBeEmpty( )->end( ) // app name
        ->scalarNode( 'client_id' )->isRequired( )->cannotBeEmpty( )->end( ) // client id
        ->scalarNode( 'client_secret' )->isRequired( )->cannotBeEmpty( )->end( ) // client secret
        ->scalarNode( 'callback_route' )->isRequired( )->cannotBeEmpty( )->end( ) // redirect callback
        ->arrayNode( 'scopes' )->prototype( 'scalar' )->isRequired( )->end( )->end( ) // scopes
        ->scalarNode( 'state' )->defaultValue( 'auth' )->end( ) // default state auth
        ->scalarNode( 'access_type' )->defaultValue( 'online' )->end( ) // default acess type online
        ->scalarNode( 'approval_prompt' )->defaultValue( 'auto' )->end( ) // 
        ->arrayNode( 'class' )->addDefaultsIfNotSet( )->children( ) // clasess
        ->scalarNode( 'api' )->defaultValue( 'FOS\GoogleBundle\Google\GoogleSessionPersistence' )->end( ) // api
        ->scalarNode( 'helper' )->defaultValue( 'FOS\GoogleBundle\Templating\Helper\GoogleHelper' )->end( ) // template helper
        ->scalarNode( 'twig' )->defaultValue( 'FOS\GoogleBundle\Twig\Extension\GoogleExtension' )->end( ) // twig ext
        ->end( ) // end clasess
        ->end( )->end( );

    return $treeBuilder;
  }
}
