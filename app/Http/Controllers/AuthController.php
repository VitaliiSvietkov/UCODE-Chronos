<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\AuthHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @param AuthHelper $authHelper
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(Request $request, AuthHelper $authHelper)
    {
        $credentials = $request->only([
            'email',
            'password',
        ]);

        $validator   = Validator::make($credentials, [
            'email'     => 'required|unique:App\Models\User,email',
            'password'  => 'required|min:6|max:14',
        ]);
        if ($validator->fails()) {
            return response([
                'message' => $validator->errors()->first()
            ], 400);
        }

        $credentials['password'] = Hash::make($credentials['password']);
        $user                    = User::create($credentials);

        Calendar::create([
            'owner_id'     => $user->id,
            'name'         => $user->email . '`s calendar',
            'has_holidays' => 0,
        ]);
        Calendar::create([
            'owner_id'     => $user->id,
            'name'         => $user->email . '`s holiday calendar',
            'has_holidays' => 1,
        ]);

        return response([
            'user'    => $user,
            'message' => 'Successfully registered'
        ]);
    }

    /**
     * @param Request $request
     * @param AuthHelper $authHelper
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \MiladRahimi\Jwt\Exceptions\InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     */
    public function login(Request $request, AuthHelper $authHelper)
    {
        $credentials = $request->only([
            'email',
            'password',
        ]);

        if (!count($credentials)) {
            return response([
                'message'  => 'Email or password is not correct'
            ], 400);
        }

        if (Auth::attempt($credentials, false)) {
            // Generate a unique key for the token creation
            $user     = Auth::user();
            $key      = Str::random(32);
            $access   = $authHelper->generateAccessToken($user, $key);
            $remember = $authHelper->generateRememberToken($user, $key);

            $authHelper->setAllAuthCookies($key, $access, $request->input('remember') === 'true');
            $authHelper->setRememberToken($user, $remember);

            return response([
                'message' => 'Successfully logged in'
            ]);
        }

        return response([
            'message'  => 'Email or password is not correct'
        ], 400);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function logout(Request $request, AuthHelper $authHelper)
    {
        $data = $authHelper->decodeToken(Cookie::get('AT'), Cookie::get('KEY'));

        if (in_array($data['status'], array(401, 200))) {
            $claims = $data['data'];
            $user   = User::find($claims['id']);
            if ($user) {
                $user->remember_token = null;
                $user->save();
            }
        }

        Cookie::queue(Cookie::forget('KEY'));
        Cookie::queue(Cookie::forget('AT'));

        return response([
            'message' => 'Logged out'
        ]);
    }

    public function me(Request $request, AuthHelper $authHelper)
    {
        $user = $authHelper->user();
        if (!$user) {
            return response([], 401);
        }

        return response()->json($authHelper->user());
    }
}
