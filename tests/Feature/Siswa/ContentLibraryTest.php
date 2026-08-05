<?php

namespace Tests\Feature\Siswa;

use App\Enums\UserRole;
use App\Livewire\Siswa\ContentLibrary;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_browse_all_content_without_restriction(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);
        Content::create(['title' => 'Artikel A', 'description' => 'Isi.', 'type' => 'artikel', 'published_at' => '2026-01-01']);
        Content::create(['title' => 'Video A', 'description' => 'Isi.', 'type' => 'video', 'video_url' => 'https://youtu.be/abc123', 'published_at' => '2026-02-01']);

        $component = Livewire::actingAs($siswa)->test(ContentLibrary::class);

        $this->assertCount(2, $component->get('contents'));
    }

    public function test_type_filter_narrows_listing(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);
        Content::create(['title' => 'Artikel A', 'description' => 'Isi.', 'type' => 'artikel', 'published_at' => '2026-01-01']);
        Content::create(['title' => 'Video A', 'description' => 'Isi.', 'type' => 'video', 'video_url' => 'https://youtu.be/abc123', 'published_at' => '2026-02-01']);

        $component = Livewire::actingAs($siswa)->test(ContentLibrary::class)
            ->set('typeFilter', 'video');

        $this->assertCount(1, $component->get('contents'));
        $this->assertSame('Video A', $component->get('contents')->first()->title);
    }

    public function test_guru_bk_can_also_view_library(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        Content::create(['title' => 'Artikel A', 'description' => 'Isi.', 'type' => 'artikel', 'published_at' => '2026-01-01']);

        $this->actingAs($guruBk)
            ->get(route('siswa.content-library'))
            ->assertOk();
    }
}
