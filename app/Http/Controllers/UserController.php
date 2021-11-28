<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * @var AuthHelper
     */
    private AuthHelper $authHelper;

    public function __construct(AuthHelper $authHelper)
    {
        $this->authHelper = $authHelper;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \MiladRahimi\Jwt\Exceptions\InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonDecodingException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     * @throws \MiladRahimi\Jwt\Exceptions\ValidationException
     */
    public function calendars(Request $request): JsonResponse
    {
        $user = $this->authHelper->user();
        $ownCalendars = $user->ownCalendars()->get();
        return response()->json($ownCalendars);
    }
}
