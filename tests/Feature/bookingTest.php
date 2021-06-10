<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\Tamagotchi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class bookingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateBooking()
    {
        $user = User::factory()->create();
        $tamagootchi = Tamagotchi::query()->create([
            'name' => 'asd',
            'user_id' => $user->id,
            'coins' => 0,
            'health'=> 0,
            'level'=> 0,
            'boredom'=> 0,
            'alive'=> true,
        ]);

        $response = $this->actingAs($user)->post('/api/book', [
            "ids" => [
                $tamagootchi->id
            ],
            "room_id" => Room::factory()->create()->id
        ]);

        $response->assertStatus(200);
    }
}
