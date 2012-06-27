<?php

/*
 * This file is part of the FOSGoogleBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\GoogleBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;

class GoogleHelper extends Helper
{
    protected $templating;

    public function __construct(EngineInterface $templating)
    {
        $this->templating  = $templating;
    }

    public function loginButton($parameters = array(), $name = null)
    {
        $name = $name ?: 'FOSGoogleBundle::loginButton.html.php';
        return $this->templating->render($name, $parameters);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'google';
    }
}
