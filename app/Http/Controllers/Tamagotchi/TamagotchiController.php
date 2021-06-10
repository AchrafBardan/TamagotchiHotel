<?php

namespace App\Http\Controllers\Tamagotchi;

use App\Models\Tamagotchi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class TamagotchiController extends Controller
{
    public function createTamagotchi(Request $req)
    {
        // validate input
        $validator = Validator::make($req->all(), [
            'name' => ['required'],
        ]);

        // check if there were any errors
        if ($validator->errors()->count()) {
            return response()->json($validator->errors(), 400);
        }

        // create a tamagotchi
        try {
            $tamagotchi = Tamagotchi::query()->create([
                'user_id' => auth()->user()->id,
                'name' => $req->name,
                'coins' => 0,
                'health' => 100,
                'level' => 0,
                'boredom' => 0,
                'alive' => true,
            ]);
        } catch (QueryException $error) {
            return response()->json($error, 400);
        }

        return response()->json([
            'message' => 'TAMAGOTCHI_CREATED',
            'tamagotchi' => $tamagotchi
        ], 200);
    }

    public function getTamagotchi()
    {
        // get the tamagotchis that belong to the user
        $tamagotchis = Tamagotchi::query()
            ->where(['user_id' => auth()->user()->id])
            ->get();

        // check if user has tamagotchis
        if ($tamagotchis->count() === 0) {
            return response()->json(['error' => 'NO_TAMAGOTCHIS'], 400);
        }

        // return the tamagotchis of the user
        return response()->json($tamagotchis, 200);
    }

    public function deleteTamagotchi(Request $req)
    {
        // validate input
        $validator = Validator::make($req->all(), [
            'id' => ['required', 'integer'],
        ]);

        // check if there were any errors
        if ($validator->errors()->count()) {
            return response()->json($validator->errors(), 400);
        }

        // get the tamagotchi from the database
        $tamagotchi = Tamagotchi::query()->where([
            'user_id' => auth()->user()->id,
            'id' => $req->id,
        ])->first();

        // check if tamagotchi exists and if it belongs to the auth user
        if(!$tamagotchi){
            return response()->json(['error' => 'TAMAGOTCHI_DOES_NOT_EXIST'], 400);
        }

        try {
            $tamagotchi->delete();
        } catch (QueryException $error) {
            return response()->json($error, 400);
        }

        return response()->json(['message' => 'TAMAGOTCHI_DELETED'], 200);
    }
}
