<?php

namespace App\Http\Controllers;

use App\Helpers\AuthHelper;
use App\Models\Calendar;
use App\Models\CalendarAccess;
use App\Models\User;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class CalendarController extends Controller
{
    /**
     * @var AuthHelper
     */
    private AuthHelper $auth;

    /**
     * CalendarController constructor.
     * @param AuthHelper $auth
     */
    public function __construct(AuthHelper $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function share(Request $request, $id): JsonResponse
    {
        $calendar = Calendar::find($id);
        if (!$calendar) {
            return response()->json([], 404);
        }

        $user = User::where('email', $request->query('email'))->first();
        if (!$user) {
            return response()->json([], 404);
        }

        if (
            $user->id === $calendar->owner_id
            || CalendarAccess::where('user_id', $user->id)->where('calendar_id', $id)->first()
        ) {
            return response()->json([], 400);
        }

        CalendarAccess::create([
           'user_id'     => $user->id,
           'calendar_id' => $id,
        ]);

        return response()->json([
            'message' => 'Success!'
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \MiladRahimi\Jwt\Exceptions\InvalidKeyException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonDecodingException
     * @throws \MiladRahimi\Jwt\Exceptions\JsonEncodingException
     * @throws \MiladRahimi\Jwt\Exceptions\SigningException
     * @throws \MiladRahimi\Jwt\Exceptions\ValidationException
     */
    public function createEvent(Request $request, $id): JsonResponse
    {
        $calendar = Calendar::find($id);
        if (!$calendar) {
            return response()->json([], 404);
        }

        $event = Event::create([
            'title'       => $request->input('title'),
            'start'       => $request->input('start'),
            'end'         => $request->input('end'),
            'author_id'   => $this->auth->user()->id,
            'calendar_id' => $calendar->id,
        ]);

        return response()->json($event);
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function getEvents($id): JsonResponse
    {
        $calendar = Calendar::find($id);
        if (!$calendar) {
            return response()->json([], 404);
        }

        return response()->json(Event::where('calendar_id', $id)->get());
    }
}
