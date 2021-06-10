<?php

namespace Tests\Feature;

use App\Models\Tamagotchi;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class tamagotchoTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCreateTamagotchi()
    {
        $response = $this->actingAs(User::factory()->create())->post('/api/tamagotchi', [
            'name' => 'asd'
        ]);

        $response->assertStatus(200);
    }

    public function testGetTamagotchi()
    {
        $user = User::factory()->create();
        Tamagotchi::query()->create([
            'name' => 'asd',
            'user_id' => $user->id,
            'coins' => 0,
            'health'=> 0,
            'level'=> 0,
            'boredom'=> 0,
            'alive'=> true,
        ]);

        $response = $this->actingAs($user)->get('/api/tamagotchi');

        $response->assertStatus(200);
    }

    public function testDeleteTamagotchi()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/api/tamagotchi', [
            'id' => Tamagotchi::query()->create([
                'name' => 'asd',
                'user_id' => $user->id,
                'coins' => 0,
                'health'=> 0,
                'level'=> 0,
                'boredom'=> 0,
                'alive'=> true,
            ])->id,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200);
    }
}
