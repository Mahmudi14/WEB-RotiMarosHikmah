<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Nama Baru',
            'email' => 'nama-baru@example.com',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));

        $user->refresh();

        $this->assertSame('Nama Baru', $user->name);
        $this->assertSame('nama-baru@example.com', $user->email);
    }

    public function test_password_can_be_updated_from_profile(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('profile.edit'));

        $this->assertTrue(
            Hash::check('new-password', $user->fresh()->password)
        );
    }

    public function test_password_is_not_updated_when_current_password_is_wrong(): void
    {
        $user = User::factory()->create([
            'role' => 'kasir',
            'status' => 'aktif',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)
            ->from('/profile')
            ->put('/profile/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect('/profile');
        $response->assertSessionHasErrors('current_password');

        $this->assertTrue(
            Hash::check('password', $user->fresh()->password)
        );
    }
}