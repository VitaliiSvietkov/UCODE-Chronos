<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use App\Models\CalendarAccess;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
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
}
