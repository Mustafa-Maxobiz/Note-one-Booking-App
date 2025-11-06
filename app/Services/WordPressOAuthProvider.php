<?php

namespace App\Services;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class WordPressOAuthProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * WordPress OAuth2 server URL
     */
    protected $serverUrl;

    /**
     * WordPress OAuth2 authorize URL
     */
    protected $authorizeUrl;

    /**
     * WordPress OAuth2 token URL
     */
    protected $tokenUrl;

    /**
     * WordPress OAuth2 user info URL
     */
    protected $userUrl;

    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        
        $this->serverUrl = $options['server_url'] ?? config('oauth.wordpress.server_url');
        $this->authorizeUrl = $options['authorize_url'] ?? config('oauth.wordpress.authorize_url');
        $this->tokenUrl = $options['token_url'] ?? config('oauth.wordpress.token_url');
        $this->userUrl = $options['user_url'] ?? config('oauth.wordpress.user_url');
    }

    /**
     * Get authorization URL to begin OAuth flow
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->authorizeUrl;
    }

    /**
     * Get access token URL to retrieve token
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->tokenUrl;
    }

    /**
     * Get provider URL to fetch user details
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->userUrl;
    }

    /**
     * Get the default scopes used by this provider
     */
    protected function getDefaultScopes()
    {
        return ['read'];
    }

    /**
     * Check a provider response for errors
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error'])) {
            throw new IdentityProviderException(
                $data['error_description'] ?? $data['error'],
                $response->getStatusCode(),
                $data
            );
        }
    }

    /**
     * Generate a user object from a successful user details request
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WordPressUser($response);
    }

    /**
     * Get the string used to separate scopes
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }
}
