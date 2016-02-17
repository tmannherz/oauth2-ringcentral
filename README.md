# RingCentral provider for OAuth 2.0 Client

This package provides RingCentral OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require tmannherz/oauth2-ringcentral
```

## Usage

Usage is the same as The League's OAuth client, using `\TMannherz\OAuth2\Client\Provider\Ringcentral` as the provider.

### Resource Owner Password Credentials Grant

```php
$provider = new TMannherz\OAuth2\Client\Provider\Ringcentral([
    'clientId' => 'rc_app_id',
    'clientSecret' => 'rc_app_secret'
]);
$provider->setDevMode();  // enable sandbox mode

try {
    // Try to get an access token using the resource owner password credentials grant.
    $accessToken = $provider->getAccessToken('password', [
        'username' => 'rc_number',
        'password' => 'rc_pass'
    ]);
} catch (\Exception $e) {
    exit($e->getMessage());
}
```

## License

The MIT License (MIT). Please see [License File](https://github.com/tmannherz/oauth2-ringcentral/blob/master/LICENSE) for more information.
