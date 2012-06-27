<?php

/*
 * This file is part of the FOSGoogleBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\GoogleBundle\Security\Firewall;

use FOS\GoogleBundle\Security\Authentication\Token\GoogleUserToken;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\HttpFoundation\Request;

/**
 * Google authentication listener.
 */
class GoogleListener extends AbstractAuthenticationListener
{
    protected function attemptAuthentication(Request $request)
    {
      if($request->get("code", null))
        return $this->authenticationManager->authenticate(new GoogleUserToken($this->providerKey));
      return null;
    }
}
