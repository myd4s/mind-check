<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Volt\Volt;
use Tests\TestCase;

class ForceChangePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_must_change_password_is_redirected_from_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Siswa,
            'password' => Hash::make('1234567890'),
            'must_change_password' => true,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('password.force-change'));
    }

    public function test_user_with_must_change_password_can_still_access_the_force_change_page(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Siswa,
            'password' => Hash::make('1234567890'),
            'must_change_password' => true,
        ]);

        $this->actingAs($user)
            ->get(route('password.force-change'))
            ->assertOk();
    }

    public function test_user_without_flag_is_not_redirected(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Siswa,
            'must_change_password' => false,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_updating_password_clears_the_flag_and_redirects_to_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Siswa,
            'password' => Hash::make('1234567890'),
            'must_change_password' => true,
        ]);

        Volt::actingAs($user)
            ->test('pages.auth.force-change-password')
            ->set('current_password', '1234567890')
            ->set('password', 'password-baru-aman')
            ->set('password_confirmation', 'password-baru-aman')
            ->call('updatePassword')
            ->assertRedirect(route('dashboard', absolute: false));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('password-baru-aman', $user->password));
    }

    public function test_updating_password_requires_correct_current_password(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Siswa,
            'password' => Hash::make('1234567890'),
            'must_change_password' => true,
        ]);

        Volt::actingAs($user)
            ->test('pages.auth.force-change-password')
            ->set('current_password', 'salah')
            ->set('password', 'password-baru-aman')
            ->set('password_confirmation', 'password-baru-aman')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);

        $this->assertTrue($user->fresh()->must_change_password);
    }

    public function test_cannot_bypass_force_change_by_navigating_directly_to_another_route(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::GuruBk,
            'password' => Hash::make('1234567890'),
            'must_change_password' => true,
        ]);

        $this->actingAs($user)
            ->get(route('guru-bk.students'))
            ->assertRedirect(route('password.force-change'));
    }
}
