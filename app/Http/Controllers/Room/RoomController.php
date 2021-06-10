<?php

namespace App\Http\Controllers\Room;

use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function createRoom(Request $req)
    {
        // validate input
        $validator = Validator::make($req->all(), [
            'size' => ['required', 'integer'],
            'type' => ['required'],
        ]);

        // check if there were any errors
        if ($validator->errors()->count()) {
            return response()->json($validator->errors(), 400);
        }

        // check if room type exists
        if(!in_array($req->type, Room::$types)){
            return response()->json([
                'error' => 'ROOM_TYPE_DOES_NOT_EXIST',
            ], 400);
        }

        // create room
        try {
            $room = Room::query()->create([
                'size' => $req->size,
                'type' => $req->type,
            ]);
        } catch (QueryException $error) {
            return response()->json($error, 400);
        }

        // return room
        return response()->json([
            'room' => $room,
            'message' => 'ROOM_CREATED'
        ], 200);
    }

    public function getRoom()
    {
        // get all rooms
        $rooms = Room::query()->get();

        // check if there arerooms
        if ($rooms->count() === 0) {
            return response()->json(['error' => 'NO_ROOMS'], 400);
        }

        return response()->json(['rooms' => $rooms], 200);
    }

    public function updateRoom(Request $req)
    {
        // validate input
        $validator = Validator::make($req->all(), [
            'id' => ['required', 'integer'],
            'type' => ['integer'],
            'size' => ['integer'],
        ]);

        // check if there were any errors
        if ($validator->errors()->count()) {
            return response()->json($validator->errors(), 400);
        }

        // get room
        $room = Room::query()->where(['id' => $req->id]);

        //check if room exists
        if(!$room->first()){
            return response()->json(['error' => 'ROOM_DOES_NOT_EXIST'], 400);
        }

        // update room
        try {
            $room->update($req->all());
        } catch (QueryException $error) {
            return response()->json($error, 400);
        }

        return response()->json(['message' => 'ROOM_UPDATED'], 200);
    }

    public function deleteRoom(Request $req)
    {
        // validate input
        $validator = Validator::make($req->all(), [
            'id' => ['required', 'integer'],
            'type' => ['integer'],
            'size' => ['integer'],
        ]);

        // check if there were any errors
        if ($validator->errors()->count()) {
            return response()->json($validator->errors(), 400);
        }

        // get room
        $room = Room::query()->where(['id' => $req->id]);

        //check if room exists
        if(!$room->first()){
            return response()->json(['error' => 'ROOM_DOES_NOT_EXIST'], 400);
        }

        try {
            $room->delete();
        } catch (QueryException $error) {
            return response()->json($error, 400);
        }

        return response()->json(['message' => 'ROOM_DELETED'], 200);
    }
}
