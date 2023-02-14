<?php

// TODO: transform in middleware and make more generic

namespace Minifram\Controller;

// use Minifram\Http\Request;
// use Minifram\Controller\User;

class AuthController {

  // public static function login(Request $request) {

  //   $data = $request->getBody();

  //   try {
  //     if(!isset($data['email']) or !isset($data['password']))
  //       throw new \Exception('Fields email and password are required', 400);

  //     $user = new User;

  //     $userData = $user->getByEmail($data['email']);

  //     if(empty($userData))
  //       throw new \Exception('User not found', 404);

  //     $data['password'] = hash('sha256', $data['password']);

  //     if($data['password'] !== $userData['password'])
  //       throw new \Exception("Incorrect password", 401);

  //     $token = self::jwtGenerate($data['email']);

  //     if ($user->getToken($userData['id'])['token'] !== $token) {
  //       $user->updateToken($userData['id'], $token);
  //     }

  //     $userData['token'] = $token;

  //     $request->sendResponse($userData, 200);

  //   } catch (\Exception $e) {
  //     $request->sendResponse(['error' => $e->getMessage()], $e->getCode());
  //   }
  // }

  // private static function jwtGenerate($email) {
  //   $key = APP_SECRET_KEY;

  //   $header = [
  //       'typ' => 'JWT',
  //       'alg' => 'HS256'
  //   ];

  //   $payload = [
  //       'email' => $email
  //   ];

  //   $header = self::base64url_encode(json_encode($header));
  //   $payload = self::base64url_encode(json_encode($payload));

  //   $data = $header.'.'.$payload;

  //   $signature = hash_hmac('sha256', $data, $key, true);
  //   $signature = self::base64url_encode($signature);

  //   return $header.'.'.$payload.'.'.$signature;
  // }

  // public static function validateToken($token, User $user) {
  //   $userData = $user->getUserByToken($token);

  //   if(empty($userData))
  //     throw new \Exception("Unable to access wrong token", 403);
  // }

  // public static function validateUser($requestToken, $id, User $user) {
  //   $userToken = $user->getToken($id);

  //   if(!isset($userToken['token']))
  //     throw new \Exception('User not found', 404);

  //   if($userToken['token'] !== $requestToken)
  //     throw new \Exception('Invalid token', 403);
  // }

  // private static function base64url_encode($data) {
  //   return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  // }

  // private static function base64url_decode($data) {
  //   return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  // }

}
