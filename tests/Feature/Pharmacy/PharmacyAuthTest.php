<?php

namespace Tests\Feature\Pharmacy;

use App\Models\Pharmacy\PharmacyUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PharmacyAuthTest extends TestCase
{
    use RefreshDatabase;

    public function it_fails_when_fields_are_missing(){
        $response = $this->postJson('/api/pharmacy/login', []);

        $response->assertStatus(422)->assertJson([
                'status' => false,
                'message' => 'Validation error'
            ]);
    }

    public function it_fails_with_invalid_credentials(){
        PharmacyUser::factory()->create([
            'mobile'=>'977627760990',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/pharmacy/login', [
            'mobile' => '977627760990',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'message' => 'Invalid credentials'
                 ]);
    }

    public function it_logs_in_successfully_and_returns_token()
    {
        $user = PharmacyUser::factory()->create([
            'mobile' => '9776388659',
            'password' => Hash::make('password123')
        ]);

        $response = $this->postJson('/api/pharmacy/login', [
            'mobile' => '9776388659',
            'password' => 'password123'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token',
                     'type'
                 ]);

        $this->assertNotEmpty($response['token']);
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
