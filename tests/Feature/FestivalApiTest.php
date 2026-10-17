<?php

namespace Tests\Feature;

use App\Models\Festival;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Mirrors the ten Bruno requests from Step 6. Run with: php artisan test
class FestivalApiTest extends TestCase
{
    use RefreshDatabase;

    private function validFestival(array $overrides = []): array
    {
        return array_merge([
            'name'          => 'Sinulog',
            'city'          => 'Cebu City',
            'month'         => 'January',
            'duration_days' => 9,
            'theme'         => 'Religious',
        ], $overrides);
    }

    private function registerAndGetToken(): string
    {
        $response = $this->postJson('/api/register', [
            'name'     => 'Test User',
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        return $response->json('token');
    }

    public function test_1_register_returns_201(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Test User', 'email' => 'test@example.com', 'password' => 'password123',
        ])->assertStatus(201);
    }

    public function test_2_login_returns_200(): void
    {
        $this->registerAndGetToken();

        $this->postJson('/api/login', [
            'email' => 'test@example.com', 'password' => 'password123',
        ])->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_3_index_without_token_returns_401(): void
    {
        $this->getJson('/api/festivals')->assertStatus(401);
    }

    public function test_4_index_with_token_returns_200_and_filters_work(): void
    {
        $token = $this->registerAndGetToken();
        Festival::create($this->validFestival());
        Festival::create($this->validFestival(['name' => 'Pahiyas', 'city' => 'Lucban', 'month' => 'May']));

        $this->withToken($token)->getJson('/api/festivals')
            ->assertStatus(200)->assertJsonCount(2);

        $this->withToken($token)->getJson('/api/festivals?city=Lucban')
            ->assertStatus(200)->assertJsonCount(1);

        $this->withToken($token)->getJson('/api/festivals?city=Lucban&month=January')
            ->assertStatus(200)->assertJsonCount(0);
    }

    public function test_5_store_valid_returns_201(): void
    {
        $token = $this->registerAndGetToken();

        $this->withToken($token)->postJson('/api/festivals', $this->validFestival())
            ->assertStatus(201)->assertJsonFragment(['name' => 'Sinulog']);
    }

    public function test_6_store_missing_field_returns_422(): void
    {
        $token = $this->registerAndGetToken();
        $data = $this->validFestival();
        unset($data['name']);

        $this->withToken($token)->postJson('/api/festivals', $data)->assertStatus(422);
    }

    public function test_7_show_returns_200(): void
    {
        $token = $this->registerAndGetToken();
        $festival = Festival::create($this->validFestival());

        $this->withToken($token)->getJson('/api/festivals/' . $festival->id)->assertStatus(200);
    }

    public function test_8_show_missing_returns_404(): void
    {
        $token = $this->registerAndGetToken();

        $this->withToken($token)->getJson('/api/festivals/99999')->assertStatus(404);
    }

    public function test_9_update_returns_200(): void
    {
        $token = $this->registerAndGetToken();
        $festival = Festival::create($this->validFestival());

        $this->withToken($token)
            ->putJson('/api/festivals/' . $festival->id, ['duration_days' => 3])
            ->assertStatus(200)->assertJsonFragment(['duration_days' => 3]);
    }

    public function test_10_destroy_returns_204(): void
    {
        $token = $this->registerAndGetToken();
        $festival = Festival::create($this->validFestival());

        $this->withToken($token)->deleteJson('/api/festivals/' . $festival->id)->assertStatus(204);
        $this->assertDatabaseMissing('festivals', ['id' => $festival->id]);
    }
}
