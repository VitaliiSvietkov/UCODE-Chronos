<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cookie;
use MiladRahimi\Jwt\Generator;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\InvalidKeyException;
use MiladRahimi\Jwt\Exceptions\InvalidSignatureException;
use MiladRahimi\Jwt\Exceptions\InvalidTokenException;

class AuthHelper
{
    public $hour;
    public $week;

    /**
     * AuthHelper constructor.
     */
    public function __construct()
    {
        $this->hour = time() + (60 * 60);
        $this->week = time() + (7 * 24 * 60 * 60);
    }

    /**
     * @param Authenticatable $user
     * @param string $key
     * @return string
     * @throws InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     */
    public function generateAccessToken(Authenticatable $user, string $key): string
    {
        // Use HS256 to generate and parse tokens
        $signer    = new HS256($key);
        // Generate a token
        $generator = new Generator($signer);

        return $generator->generate([
            'id'      => $user->id,
            'role'    => $user->role,
            'expires' => time() + (10 * 60)
        ]);
    }

    /**
     * @param Authenticatable $user
     * @param string $key
     * @param bool $remember
     * @return string
     * @throws InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     */
    public function generateRememberToken(Authenticatable $user, string $key, bool $remember = false): string
    {
        if ($remember) {
            $expires = $this->week;
        } else {
            $expires = $this->hour;
        }

        // Use HS256 to generate and parse tokens
        $signer    = new HS256($key);
        // Generate a token
        $generator = new Generator($signer);

        return $generator->generate([
            'id'      => $user->id,
            'role'    => $user->role,
            'expires' => $expires
        ]);
    }

    /**
     * @param string $token
     * @param string $key
     * @return array
     * @throws \MiladRahimi\Jwt\Exceptions\JsonDecodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     * @throws \MiladRahimi\Jwt\Exceptions\ValidationException
     */
    public function decodeToken(string $token, string $key): array
    {
        // Parse the token
        try {
            $parser = new Parser(new HS256($key));
        }
        catch (InvalidKeyException $e) {
            return [
                'status'  => 400,
                'message' => 'Invalid key',
            ];
        }

        // Get payload from token
        try {
            $claims = $parser->parse($token);
            if ($claims['expires'] < time()) {
                return [
                    'status'  => 401,
                    'data'    => $claims,
                    'message' => 'Session has already expired',
                ];
            }

            return [
                'status' => 200,
                'data'   => $claims,
            ];
        }
        catch (InvalidSignatureException | InvalidTokenException $e) {
            return [
                'status'  => 400,
                'message' => 'Invalid token',
            ];
        }
    }

    /**
     * @param Authenticatable $user
     * @param string $key
     * @return array
     * @throws InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonDecodingException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     * @throws \MiladRahimi\Jwt\Exceptions\ValidationException
     */
    public function resetAccessToken(Authenticatable $user, string $key): array
    {
        $remember_claims = $this->decodeToken($user->remember_token, $key);
        if ($remember_claims['status'] !== 200) {
            return $remember_claims;
        }

        $user  = User::find($remember_claims['id']);
        $token = $this->generateAccessToken($user, $key);
        $this->queueAT($token);

        return [
            'status' => 200,
            'token'  => $token,
        ];
    }

    /**
     * @param string $key
     * @param string $access
     * @param bool $remember
     */
    public function setAllAuthCookies(string $key, string $access, bool $remember)
    {
        if ($remember) {
            $this->queueKey($key, $this->week);
        } else {
            $this->queueKey($key, $this->hour);
        }
        $this->queueAT($access);
    }

    /**
     * @param string $token
     */
    public function queueAT(string $token): void
    {
        Cookie::queue('AT', $token, $this->hour, '/');
    }

    /**
     * @param string $key
     * @param bool $remember
     */
    public function queueKey(string $key, bool $remember = false): void
    {
        if ($remember) {
            Cookie::queue('KEY', $key, $this->week, '/');
        } else {
            Cookie::queue('KEY', $key, $this->hour, '/');
        }
    }

    /**
     * @param Authenticatable $user
     * @param string $token
     */
    public function setRememberToken(Authenticatable $user, string $token): void
    {
        $user->remember_token = $token;
        $user->save();
    }

    /**
     * @return null|User
     * @throws InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonDecodingException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     * @throws \MiladRahimi\Jwt\Exceptions\ValidationException
     */
    public function user()
    {
        if (!Cookie::has('AT') || !Cookie::has('KEY')) {
            return null;
        }

        $user   = null;
        $data = $this->decodeToken(Cookie::get('AT'), Cookie::get('KEY'));

        if ($data['status'] === 400) {
            return null;
        }

        $claims = $data['data'];
        $user   = User::find($claims['id']);
        if ($data['status'] === 401) {
            if ($this->resetAccessToken($user, Cookie::get('KEY'))['status'] !== 200) {
                return null;
            }
        }

        return $user;
    }
}
