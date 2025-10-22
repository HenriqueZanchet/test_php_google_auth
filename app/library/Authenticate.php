<?php
namespace app\library;

use app\database\models\User;

class Authenticate
{
  public function authGoogle($data)
  {
    $user = new User;
    $userFound = $user->findBy('email', $data->email);
    if (!$userFound) {
      $user->insert([
        'firstName' => $data->givenName,
        'lastName' => $data->familyName,
        'email' => $data->email,
        'picture' => $data->picture,
      ]);
    }

    if (!$userFound) {
      $userFound = $user->findBy('email', $data->email);
    }


    $_SESSION['user'] = $userFound ?: (object) [
      'firstName' => $data->givenName,
      'lastName' => $data->familyName,
      'email' => $data->email,
      'picture' => $data->picture,
    ];
    $_SESSION['auth'] = true;
    header('Location: /home');
    exit; // interrompe execução após redirect
  }

  public function auth()
  {

  }

  public function logout()
  {
    unset($_SESSION['user'], $_SESSION['auth']);
    header('Location:/');

  }
}