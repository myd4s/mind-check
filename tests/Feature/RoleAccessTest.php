<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'role:guru_bk'])
            ->get('/_test/guru-bk-only', fn () => 'ok');
    }

    public function test_admin_can_access_guru_bk_protected_route(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        $this->actingAs($admin)
            ->get('/_test/guru-bk-only')
            ->assertOk();
    }

    public function test_guru_bk_can_access_guru_bk_protected_route(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $this->actingAs($guruBk)
            ->get('/_test/guru-bk-only')
            ->assertOk();
    }

    public function test_siswa_cannot_access_guru_bk_protected_route(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get('/_test/guru-bk-only')
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/_test/guru-bk-only')
            ->assertRedirect('/login');
    }
}
