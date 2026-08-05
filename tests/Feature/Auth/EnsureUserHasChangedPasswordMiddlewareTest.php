<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Http\Middleware\EnsureUserHasChangedPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsureUserHasChangedPasswordMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regresi: middleware ini sebelumnya memblokir request AJAX Livewire
     * (POST /livewire/update) untuk aksi updatePassword itu sendiri, membuat
     * deadlock — pengguna tidak pernah bisa benar-benar mengganti password
     * karena request penggantinya sendiri diblokir sebelum sampai ke method.
     */
    public function test_livewire_ajax_request_is_not_redirected_even_if_must_change_password(): void
    {
        $user = User::factory()->create(['role' => UserRole::Siswa, 'must_change_password' => true]);

        Route::middleware(['web', 'auth', EnsureUserHasChangedPassword::class])
            ->post('/_test/livewire-update', fn () => 'ok');

        $response = $this->actingAs($user)
            ->withHeader('X-Livewire', 'true')
            ->post('/_test/livewire-update');

        $response->assertOk();
    }

    public function test_regular_get_request_is_still_redirected_when_must_change_password(): void
    {
        $user = User::factory()->create(['role' => UserRole::Siswa, 'must_change_password' => true]);

        Route::middleware(['web', 'auth', EnsureUserHasChangedPassword::class])
            ->get('/_test/some-page', fn () => 'ok');

        $response = $this->actingAs($user)->get('/_test/some-page');

        $response->assertRedirect(route('password.force-change'));
    }
}
