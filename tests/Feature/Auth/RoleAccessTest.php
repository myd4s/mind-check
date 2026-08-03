<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_login_redirects_to_student_dashboard(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.role', 'student');

        $component->call('login');

        $component->assertHasNoErrors()->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $this->get(route('dashboard'))->assertRedirect(route('student.dashboard'));
    }

    public function test_counselor_login_redirects_to_counselor_dashboard(): void
    {
        $user = User::factory()->counselor()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.role', 'counselor');

        $component->call('login');

        $component->assertHasNoErrors();

        $this->get(route('dashboard'))->assertRedirect(route('counselor.dashboard'));
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.role', 'admin');

        $component->call('login');

        $component->assertHasNoErrors();

        $this->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));
    }

    public function test_login_fails_when_selected_role_does_not_match_account(): void
    {
        $user = User::factory()->create();

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.role', 'admin');

        $component->call('login');

        $component->assertHasErrors('form.email');
        $this->assertGuest();
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_student_cannot_access_counselor_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('counselor.dashboard'))
            ->assertForbidden();
    }
}
