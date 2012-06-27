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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;

class FOSGoogleExtension extends Extension
{
    protected $resources = array(
        'google' => 'google.xml',
        'security' => 'security.xml'
    );

    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, $configs);

        $this->loadDefaults($container);

        if (isset($config['alias'])) {
            $container->setAlias($config['alias'], 'fos_google.api');
        }

        foreach (array('api', 'helper', 'twig') as $attribute) {
            $container->setParameter('fos_google.'.$attribute.'.class', $config['class'][$attribute]);
        }

        foreach (array('app_name', 'client_id', 'client_secret', 'redirect_uri', 'state', 'access_type', 'approval_prompt', 'scopes') as $attribute) {
            $container->setParameter('fos_google.'.$attribute, $config[$attribute]);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function getXsdValidationBasePath()
    {
        return __DIR__ . '/../Resources/config/schema';
    }

    /**
     * @codeCoverageIgnore
     */
    public function getNamespace()
    {
        return 'http://symfony.com/schema/dic/fos_google';
    }

    /**
     * @codeCoverageIgnore
     */
    protected function loadDefaults($container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        foreach ($this->resources as $resource) {
            $loader->load($resource);
        }
    }
}
