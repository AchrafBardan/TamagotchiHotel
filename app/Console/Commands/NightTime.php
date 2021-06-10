<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Tamagotchi;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;

class NightTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:night';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Do one night cycle';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tamagotchis = Tamagotchi::query()->get();

        foreach ($tamagotchis as $tamagotchi) {
            $health = 0;
            $boredom = 0;
            $level = 0;
            $coins = 0;
            // get last booking
            $lastBooking = Booking::query()->where(['tamagotchi_id' => $tamagotchi->id])->with('room')->first();
            $room = $lastBooking->room;

            // default mutations
            $level++;
            if($tamagotchi->boredom >= 70){
                $health -= 20;
            }

            if(!$lastBooking){
                // check if tamagotchi is already in this room
                $health -= 20;
                $boredom += 20;
            }
            else{
                switch ($room->type) {
                    case 'RELAX':
                        $coins -= 10;
                        $health += 20;
                        $boredom += 10;
                        break;
                    case 'GAME':
                        $coins -= 20;
                        $boredom = 'RESET';
                        break;
                    case 'WORKING':
                        $coins += rand(10, 60);
                        $boredom += 20;
                        break;
                    case 'FIGHTING':
                        $fight = $this->fight($lastBooking->room_id, $tamagotchi->id);
                        $coins += $fight['coins'];
                        $level += $fight['level'];
                        $level += $fight['health'];
                        break;
                }

                $tamagotchi->update([
                    'health' => $tamagotchi->health + $health,
                    'coins' => $tamagotchi->coins + $coins,
                    'level' => $tamagotchi->level + $level,
                    'boredom' => ($boredom === 'RESET') ? 0 : $tamagotchi->boredom + $boredom,
                ]);

            }
        }

        echo 'Night cycle succesfull';
        return;
    }

    private function fight($room_id, $tamagotchi_id) {

        $booking = Booking::query()->where(['room_id' => $room_id])->where('tamagotchi_id','<>',$tamagotchi_id)->first();
        if($booking){
            $enemy = Tamagotchi::query()->where(['id' => $booking->tamagotchi_id])->first();
        }
        else{
            return [
                'coins' => 0,
                'level' => 0,
                'health' => 0,
            ];
        }

        switch (rand(0, 1)) {
            case 0: // win
                $enemy->update([
                    'coins' => $enemy->coins + -20,
                    'health' => $enemy->health + -30,
                ]);
                return [
                    'coins' => 20,
                    'level' => 1,
                    'health' => 0,
                ];
            case 1: // lose
                $enemy->update([
                    'coins' => $enemy->coins + 20,
                    'level' => $enemy->level + 1,
                ]);
                return [
                    'coins' => -20,
                    'level' => 0,
                    'health' => -30,
                ];
                break;
        }
    }
}
