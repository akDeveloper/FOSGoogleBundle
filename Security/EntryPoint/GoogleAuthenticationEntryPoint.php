<?php

/*
 * This file is part of the FOSGoogleBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\GoogleBundle\Security\EntryPoint;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * GoogleAuthenticationEntryPoint starts an authentication via Google.
 *
 */
class GoogleAuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected $googleApi;

    public function __construct(\apiClient $googleApi)
    {
        $this->googleApi = $googleApi;
    }

    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->googleApi->createAuthUrl());
    }
}
