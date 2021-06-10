<?php

namespace App\Http\Controllers\Booking;

use App\Models\Tamagotchi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    public function createBooking(Request $req)
    {
        // validate all inputs
        $validator = Validator::make($req->all(), [
            'room_id' => ['required','integer'],
        ]);

        // check if there were any errors
        if($validator->errors()->count()){
            return $validator->errors();
        }

        $room = Room::query()->where(['id' => $req->room_id])->first();

        // check if room exists
        if(!$room){
            return response()->json(['error' => 'ROOM_DOES_NOT_EXIST'], 400);
        }

        $tamagotchi_ids = $req->json('ids');
        // check if there if there are enough spots
        $bookings = Booking::query()->where(['room_id' => $req->room_id])->get()->count();
        if(($room->size - count($tamagotchi_ids) - $bookings) < 0){
            return response()->json(['error' => 'ROOM_FULL'], 400);
        }

        foreach ($tamagotchi_ids as $id) {
            // check if user owns tamagotchi \
            if (!$this->checkTamagotchiOwner($id)) {
                return response()->json([
                    'error' => 'TAMAGOTCHI_DOES_NOT_EXIST',
                    'id' => $id,
                ], 400);
            }

            // get last booking
            $lastBooking = Booking::query()->where(['tamagotchi_id' => $id])->first();

            if($lastBooking){
                // check if tamagotchi is already in this room
                if($lastBooking->room_id === $req->room_id){
                    return response()->json([
                        'error' => 'TAMAGOTCHI_ALREADY_IN_THIS_ROOM',
                        'id' => $id,
                    ], 400);
                }
            }

            // book room
            try {
                Booking::query()->create([
                    'room_id' => $req->room_id,
                    'tamagotchi_id' => $id,
                ]);
            } catch (QueryException $error) {
                return response()->json($error, 400);
            }
        }

        return response()->json(['message' => 'ROOMS_BOOKED'], 200);
    }

    private function checkTamagotchiOwner($id)
    {
        $tamagotchi = Tamagotchi::where([
            'id' => $id,
            'user_id' => auth()->user()->id
        ])->first();

        if ($tamagotchi) {
            return true;
        } else {
            return false;
        }
    }
}
