<?php
namespace app\library;

use Google\Client;
use Google\Service\OAuth2 as ServiceOAuth2;
use GuzzleHttp\Client as GuzzleClient;
use Google\Service\Oauth2\Userinfo;

class GoogleClient
{
  public readonly Client $client;
  protected Userinfo $data;
  public function __construct()
  {
    $this->client = new Client();
  }

  public function init()
  {
    $guzzleClient = new GuzzleClient(['curl' => [CURLOPT_SSL_VERIFYPEER => false]]);
    $this->client->setHttpClient($guzzleClient);
    $this->client->setAuthConfig('credentials.json');
    $this->client->setRedirectUri('http://localhost:8000');
    $this->client->addScope('email');
    $this->client->addScope('profile');
  }

  public function generateAuthLink()
  {
    return $this->client->createAuthUrl();
  }

  public function authorized()
  {
    if (isset($_GET['code'])) {
      $token = $this->client->fetchAccessTokenWithAuthCode($_GET['code']);

      $this->client->setAccessToken($token);
      $googleService = new ServiceOAuth2($this->client);
      $this->data = $googleService->userinfo->get();

      return true;

    }
    return false;

  }

  public function getData()
  {
    return $this->data;
  }

}
