<?php

namespace TMannherz\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * RingCentral OAuth 2 Provider.
 */
class RingCentral extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string Key used in a token response to identify the resource owner.
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'owner_id';

    /**
     * @var bool
     */
    protected $devMode = false;

    /**
     * @var string
     */
    protected $apiVersion = '1.0';

    /**
     * @var array
     */
    protected $apiUrls = [
        'dev' => 'https://platform.devtest.ringcentral.com/restapi',
        'production' => 'https://platform.ringcentral.com/restapi'
    ];

    /**
     * @param array $options
     * @param array $collaborators
     */
    public function __construct ($options = [], array $collaborators = [])
    {
        parent::__construct($options);
        if (isset($options['devMode'])) {
            $this->setDevMode($options['devMode']);
        }
    }

    /**
     * Enable requests to the sandbox server.
     *
     * @param bool $flag
     * @return $this
     */
    public function setDevMode ($flag = true)
    {
        $this->devMode = (bool)$flag;
        return $this;
    }

    /**
     * @return string
     */
    protected function getBaseUrl ()
    {
        return $this->devMode ? $this->apiUrls['dev'] : $this->apiUrls['production'];
    }

    /**
     * Get authorization url to begin OAuth flow.
     *
     * Not implemented.
     *
     * @return string
     */
    public function getBaseAuthorizationUrl ()
    {
        return null;
    }

    /**
     * Get access token url to retrieve token
     *
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl (array $params)
    {
        return $this->getBaseUrl() .  '/oauth/token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl (AccessToken $token)
    {
        return $this->getBaseUrl() .  '/v' . $this->apiVersion . '/account/' . $token->getResourceOwnerId();
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes ()
    {
        return ['ReadAccounts'];
    }

    /**
     * Set a basic auth header as required by RingCentral.
     *
     * @return array
     */
    protected function getDefaultHeaders ()
    {
        return ['Authorization' => 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)];
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  string $data Parsed response data
     */
    protected function checkResponse (ResponseInterface $response, $data)
    {
        if (isset($data['errors'])) {
            throw new IdentityProviderException(
                $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return RingcentralResourceOwner
     */
    protected function createResourceOwner (array $response, AccessToken $token)
    {
        return new RingcentralResourceOwner($response);
    }
}
