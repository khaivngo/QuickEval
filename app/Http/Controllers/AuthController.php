<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login (Request $request) {
      $http = new \GuzzleHttp\Client;

      try {
        $response = $http->post(config('services.passport.login_endpoint'), [
          'form_params' => [
            'grant_type'    => 'password',
            'client_id'     => config('services.passport.client_id'),
            'client_secret' => config('services.passport.client_secret'),
            'username'      => $request->username,
            'password'      => $request->password,
          ]
        ]);

        return $response->getBody();
      } catch (\GuzzleHttp\Exception\BadResponseException $e) {
        switch ($e->getCode()) {
          case 400:
            return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            break;

          case 401:
            return response()->json('Your credentials are incorrect. Please try again', $e->getCode());
            break;
        }

        return response()->json('Something went wrong on the server.', $e->getCode());
      }
    }

    public function anonymous () {
      $auth_id = uniqid();
      $anonymous = \App\User::create([
        'name'     => 'anonymous',
        'email'    => $auth_id . '@anonymous.com',
        'password' => '',
        'auth_id'  => $auth_id,
        'role'     => 3
      ]);

      $http = new \GuzzleHttp\Client;
      $response = $http->post(config('services.passport.login_endpoint'), [
        'form_params' => [
          'grant_type'    => 'anonymous',
          'client_id'     => config('services.passport.client_id'),
          'client_secret' => config('services.passport.client_secret'),
          // 'auth_id'    => 'some-unique-identifier',
          'auth_id'       => $anonymous->auth_id,
          // 'scope'      => '',
        ]
      ]);

      // return json_decode((string) $response->getBody(), true);
      return $response->getBody();
    }

    /**
     *
     */
    public function logout () {
      auth()->user()->tokens->each(function ($token, $key) {
        $token->delete();
      });

      return response()->json('Logged out successfully', 200);
    }

    /**
     *
     */
    public function register (Request $request) {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8'
        ]);

        $user = \App\User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'gender'        => $request->gender,
            'year_of_birth' => $request->yob,
            'institution'   => $request->institution,
            'nationality'   => $request->nationality
        ]);

        if ($request->scientist === true) {
          \App\ScientistRequest::create([
            'user_id'  => $user->id,
            'accepted' => 0
          ]);
          // use App\Mail\OrderShipped;
          // Mail::to('robin.vigdal.bekkevold@gmail.com')->send(new \App\Mail\ScientistRequest($user));

          // Mail::to($user->email)->send(new \App\Mail\Receipt($user));
        }

        return $user;
    }
}
