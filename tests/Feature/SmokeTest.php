<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    /**
     * Smoke-test the main pages under Laravel 10 against mosque_test.
     * Seeds its own data; runs on the isolated test database only.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a verified admin user to authenticate with
        $this->admin = User::updateOrCreate(
            ['email' => 'smoketest@example.com'],
            [
                'name' => 'Smoke Tester',
                'password' => bcrypt('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_real_login_with_credentials_works(): void
    {
        $response = $this->post('/login', [
            'email' => 'smoketest@example.com',
            'password' => 'password123',
        ]);
        $this->assertAuthenticated();
        $response->assertRedirect();
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')->assertStatus(302)->assertRedirect('/login');
    }

    public function test_authenticated_dashboard_renders(): void
    {
        $user = User::where('email', 'smoketest@example.com')->first();
        $this->actingAs($user)->get('/dashboard')->assertStatus(200);
    }

    public function test_donations_index_renders_for_admin(): void
    {
        $user = User::where('email', 'smoketest@example.com')->first();
        $this->actingAs($user)->get('/donations')->assertStatus(200);
    }

    public function test_leaderboard_renders(): void
    {
        $user = User::where('email', 'smoketest@example.com')->first();
        $this->actingAs($user)->get('/gamification/leaderboard')->assertStatus(200);
    }

    public function test_logout_works_and_redirects_home(): void
    {
        $user = User::where('email', 'smoketest@example.com')->first();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
