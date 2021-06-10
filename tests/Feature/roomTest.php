<?php

namespace Tests\Feature;

use App\Models\Room;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class roomTest extends TestCase
{
    public function testGetRoom()
    {
        $response = $this->actingAs(User::factory()->create())->get('/api/room');


        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateRoom()
    {
        $response = $this->actingAs(User::factory()->create())->post('/api/room', [
            'size' => 2,
            'type' => 'GAME'
        ]);


        $response->assertStatus(200);
    }

    public function testUpdateRoom()
    {
        $room = Room::factory()->create();

        $response = $this->actingAs(User::factory()->create())->put('/api/room', [
            'id' => $room->id,
            'size' => 5,
            'type' => 'WORK'
        ]);

        $response->assertStatus(200);
    }

    public function testDeleteRoom()
    {
        $room = Room::factory()->create();

        $response = $this->actingAs(User::factory()->create())->delete('/api/room',[
            'id' => $room->id
        ]);

        $response->assertStatus(200);
    }
}
