<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_route_is_disabled_when_config_is_false(): void
    {
        config(['auth.registration_enabled' => false]);

        $response = $this->get('/register');

        // Should return 404 since route is conditionalized in routes/auth.php
        $response->assertStatus(404);
    }

    public function test_registration_link_is_hidden_when_disabled(): void
    {
        config(['auth.registration_enabled' => false]);

        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('/register');
    }

    public function test_setup_artisan_command_creates_admin_user(): void
    {
        $this->artisan('spectora:setup')
            ->expectsQuestion('Enter administrator first name', 'Alice')
            ->expectsQuestion('Enter administrator last name', 'Smith')
            ->expectsQuestion('Enter administrator email address', 'alice@spectora.test')
            ->expectsQuestion('Enter administrator password (min. 8 characters)', 'supersecret123')
            ->expectsQuestion('Confirm administrator password', 'supersecret123')
            ->expectsOutputToContain('Success! Administrator account created.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'alice@spectora.test',
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'is_admin' => true,
            'timezone' => 'UTC',
        ]);

        $user = User::where('email', 'alice@spectora.test')->first();
        $this->assertTrue(Hash::check('supersecret123', $user->password));
    }

    public function test_setup_artisan_command_validates_inputs(): void
    {
        // 1. Existing user email check
        User::factory()->create(['email' => 'existing@spectora.test']);

        $this->artisan('spectora:setup')
            ->expectsQuestion('Enter administrator first name', 'Bob')
            ->expectsQuestion('Enter administrator last name', 'Jones')
            // First try invalid email, then already existing, then correct email
            ->expectsQuestion('Enter administrator email address', 'invalid-email')
            ->expectsQuestion('Enter administrator email address', 'existing@spectora.test')
            ->expectsQuestion('Enter administrator email address', 'bob@spectora.test')
            // First try empty password, then short password, then password mismatch, then correct password
            ->expectsQuestion('Enter administrator password (min. 8 characters)', '')
            ->expectsQuestion('Enter administrator password (min. 8 characters)', 'short')
            ->expectsQuestion('Enter administrator password (min. 8 characters)', 'validpassword')
            ->expectsQuestion('Confirm administrator password', 'differentpassword')
            ->expectsQuestion('Enter administrator password (min. 8 characters)', 'validpassword')
            ->expectsQuestion('Confirm administrator password', 'validpassword')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'bob@spectora.test',
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'is_admin' => true,
        ]);
    }

    public function test_non_admins_cannot_access_user_management_endpoints(): void
    {
        $user = User::factory()->create(['is_admin' => false]);
        $anotherUser = User::factory()->create();

        // 1. Guest post request
        $this->post('/users', [])->assertRedirect('/login');

        // 2. Regular user post request
        $this->actingAs($user)
            ->post('/users', [
                'first_name' => 'Hack',
                'last_name' => 'Er',
                'email' => 'hacker@spectora.test',
                'password' => 'password123',
                'timezone' => 'UTC',
                'is_admin' => true,
            ])
            ->assertStatus(403);

        // 3. Regular user delete request
        $this->actingAs($user)
            ->delete("/users/{$anotherUser->id}")
            ->assertStatus(403);
    }

    public function test_admins_can_create_new_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->post('/users', [
                'first_name' => 'Staff',
                'last_name' => 'Member',
                'email' => 'staff@spectora.test',
                'password' => 'password123',
                'timezone' => 'Europe/Berlin',
                'is_admin' => 'on',
            ])
            ->assertRedirect()
            ->assertSessionHas('status', 'user-created');

        $this->assertDatabaseHas('users', [
            'email' => 'staff@spectora.test',
            'first_name' => 'Staff',
            'last_name' => 'Member',
            'is_admin' => true,
            'timezone' => 'Europe/Berlin',
        ]);
    }

    public function test_admins_can_delete_other_users(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $otherUser = User::factory()->create();

        $this->actingAs($admin)
            ->delete("/users/{$otherUser->id}")
            ->assertRedirect()
            ->assertSessionHas('status', 'user-deleted');

        $this->assertModelMissing($otherUser);
    }

    public function test_admins_cannot_delete_themselves(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->delete("/users/{$admin->id}")
            ->assertRedirect()
            ->assertSessionHasErrors(['delete_user']);

        $this->assertModelExists($admin);
    }

    public function test_admin_can_delete_another_admin_when_multiple_exist(): void
    {
        $adminA = User::factory()->create(['is_admin' => true]);
        $adminB = User::factory()->create(['is_admin' => true]);

        $this->actingAs($adminA)
            ->delete("/users/{$adminB->id}")
            ->assertRedirect()
            ->assertSessionHas('status', 'user-deleted');

        $this->assertEquals(1, User::where('is_admin', true)->count());
    }
}
