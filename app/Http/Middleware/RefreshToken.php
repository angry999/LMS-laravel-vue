<?php

namespace App\Http\Middleware;

use Tymon\JWTAuth\Http\Middleware\RefreshToken as BaseMiddleware;

class RefreshToken extends BaseMiddleware
{
    /**
     * Set the authentication header.
     *
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse  $response
     * @param  string|null  $token
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function setAuthenticationHeader($response, $token = null)
    {
        $token = $token ?: $this->auth->refresh();
        return $response->withCookie(cookie()->forever('token', $token));
    }
}