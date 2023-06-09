<?php

namespace Celysium\Launcher\Controllers;

use Celysium\Base\Controller\Controller;
use Celysium\Responser\Responser;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException;

class AuthController extends Controller
{
    /**
     * @throws ValidationException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'password' => ['required', 'string']
        ]);
        $hash = Cache::store('file')->get('launcher_secret');
        if (!Hash::check($request->post('password'), $hash)) {
            return Responser::unprocessable(
                ValidationException::withMessages([
                    'password' => __('auth.password')
                ])->errors()
            );
        }
        if (Cache::store('file')->has('launcher_token')) {
            return Responser::unauthorized(messages: [[
                                                          'type' => 'error',
                                                          'text' => 'The user is logged. try again later.'
                                                      ]]);
        }

        $token = Hash::make(Cache::store('file')->get('launcher_secret'));
        Cache::store('file')->put('launcher_token', $token, config('token_lifetime', 300));

        return Responser::success([
            'token' => $token
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Cache::store('file')->forget('launcher_token');
        return Responser::success();
    }
}
