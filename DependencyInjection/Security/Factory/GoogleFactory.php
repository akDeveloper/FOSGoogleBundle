<?php

/*
 * This file is part of the FOSGoogleBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\GoogleBundle\DependencyInjection\Security\Factory;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class GoogleFactory extends AbstractFactory
{
    public function __construct()
    {
        $this->addOption('create_user_if_not_exists', false);
    }

    public function getPosition()
    {
        return 'pre_auth';
    }

    public function getKey()
    {
        return 'fos_google';
    }

    protected function getListenerId()
    {
        return 'fos_google.security.authentication.listener';
    }

    protected function createAuthProvider(ContainerBuilder $container, $id, $config, $userProviderId)
    {
        $authProviderId = 'fos_google.auth.'.$id;

        $definition = $container
            ->setDefinition($authProviderId, new DefinitionDecorator('fos_google.auth'))
            ->replaceArgument(0, $id);

        // with user provider
        if (isset($config['provider'])) {
            $definition
                ->addArgument(new Reference($userProviderId))
                ->addArgument(new Reference('security.user_checker'))
                ->addArgument($config['create_user_if_not_exists'])
            ;
        }

        return $authProviderId;
    }

    protected function createEntryPoint($container, $id, $config, $defaultEntryPointId)
    {
        $entryPointId = 'fos_google.security.authentication.entry_point.'.$id;
        $container->setDefinition($entryPointId, new DefinitionDecorator('fos_google.security.authentication.entry_point'));

        // set options to container for use by other classes
        $container->setParameter('fos_google.options.'.$id, $config);

        return $entryPointId;
    }
}
