<?php

/*
 * This file is part of the FOSGoogleBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\GoogleBundle\Twig\Extension;

use FOS\GoogleBundle\Templating\Helper\GoogleHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GoogleExtension extends \Twig_Extension
{
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            'google_login_button' => new \Twig_Function_Method($this, 'renderLoginButton', array('is_safe' => array('html'))),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'google';
    }

    /**
     * @see GoogleHelper::loginButton()
     */
    public function renderLoginButton($parameters = array(), $name = null)
    {
        return $this->container->get('fos_google.helper')->loginButton($parameters, $name ?: 'FOSGoogleBundle::loginButton.html.twig');
    }
}
