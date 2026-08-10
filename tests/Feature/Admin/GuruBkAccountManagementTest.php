<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\GuruBkAccountManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GuruBkAccountManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_bk_cannot_access_page(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        $this->actingAs($guruBk)
            ->get(route('admin.guru-bk-accounts'))
            ->assertForbidden();
    }

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('admin.guru-bk-accounts'))
            ->assertForbidden();
    }

    public function test_admin_can_create_guru_bk_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(GuruBkAccountManagement::class)
            ->call('create')
            ->set('name', 'Guru BK Baru')
            ->set('email', 'guru.baru@mindcare.com')
            ->set('password', 'rahasia123')
            ->call('save');

        $this->assertDatabaseHas('users', [
            'name' => 'Guru BK Baru',
            'email' => 'guru.baru@mindcare.com',
            'role' => UserRole::GuruBk->value,
            'is_active' => true,
        ]);
    }

    public function test_new_guru_bk_account_can_login_immediately(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);

        Livewire::actingAs($admin)
            ->test(GuruBkAccountManagement::class)
            ->call('create')
            ->set('name', 'Guru BK Baru')
            ->set('email', 'guru.baru@mindcare.com')
            ->set('password', 'rahasia123')
            ->call('save');

        $this->post('/logout');

        $response = $this->post('/login', [
            'email' => 'guru.baru@mindcare.com',
            'password' => 'rahasia123',
        ]);

        $this->assertAuthenticated();
    }

    public function test_admin_can_edit_guru_bk_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk, 'name' => 'Nama Lama', 'email' => 'lama@mindcare.com']);

        Livewire::actingAs($admin)
            ->test(GuruBkAccountManagement::class)
            ->call('edit', $guruBk->id)
            ->set('name', 'Nama Baru')
            ->call('save');

        $this->assertDatabaseHas('users', ['id' => $guruBk->id, 'name' => 'Nama Baru', 'email' => 'lama@mindcare.com']);
    }

    public function test_admin_can_deactivate_and_reactivate_guru_bk_account(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk, 'is_active' => true]);

        $component = Livewire::actingAs($admin)->test(GuruBkAccountManagement::class);

        $component->call('toggleActive', $guruBk->id);
        $this->assertDatabaseHas('users', ['id' => $guruBk->id, 'is_active' => false]);

        $component->call('toggleActive', $guruBk->id);
        $this->assertDatabaseHas('users', ['id' => $guruBk->id, 'is_active' => true]);
    }

    public function test_deactivated_account_cannot_login(): void
    {
        User::factory()->create([
            'role' => UserRole::GuruBk,
            'email' => 'nonaktif@mindcare.com',
            'password' => 'rahasia123',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => 'nonaktif@mindcare.com',
            'password' => 'rahasia123',
        ]);

        $this->assertGuest();
    }

    public function test_email_must_be_unique(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        User::factory()->create(['role' => UserRole::GuruBk, 'email' => 'dipakai@mindcare.com']);

        Livewire::actingAs($admin)
            ->test(GuruBkAccountManagement::class)
            ->call('create')
            ->set('name', 'Guru Baru')
            ->set('email', 'dipakai@mindcare.com')
            ->set('password', 'rahasia123')
            ->call('save')
            ->assertHasErrors(['email']);
    }
}
