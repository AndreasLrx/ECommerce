<?php


namespace App\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Symfony\Component\HttpFoundation\Response;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Authenticator\JWTAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class Authenticator extends JWTAuthenticator
{
    ## Authentication is require (no token provided)
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $response = parent::start($request, $authException);
        $response->setContent(json_encode(['error' => "Bearer token not found"]));
        return $response;
    }

    ## Bearer token is invalid (maybe expired)
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = parent::onAuthenticationFailure($request, $exception);
        if ($exception instanceof ExpiredTokenException) {
            $response->setContent(json_encode(["error" => "Bearer token expired"]));
        } else {
            $response->setContent(json_encode(["error" => "Bearer token is invalid"]));
        }
        return $response;
    }
}
