<?php

namespace App\GrantTypes;

/**
 * Interface GrantTypeInterface.
 */
interface GrantTypeInterface
{
    /**
     * Obtain the token data returned by the OAuth2 server.
     *
     * @param string $refreshToken
     *
     * @return array API token
     * @throws \Bmatovu\OAuthNegotiator\Exceptions\TokenRequestException
     *
     */
    public function getToken($refreshToken = null);

}