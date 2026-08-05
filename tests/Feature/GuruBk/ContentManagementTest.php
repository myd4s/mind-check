<?php

namespace Tests\Feature\GuruBk;

use App\Enums\UserRole;
use App\Livewire\GuruBk\ContentManagement;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ContentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_cannot_access_page(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);

        $this->actingAs($siswa)
            ->get(route('guru-bk.contents'))
            ->assertForbidden();
    }

    public function test_guru_bk_can_create_article(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(ContentManagement::class)
            ->call('create')
            ->set('title', 'Mengelola Stress Ujian')
            ->set('description', 'Tips mengelola stress menjelang ujian.')
            ->set('type', 'artikel')
            ->set('author', 'Guru BK Dummy')
            ->set('published_at', '2026-08-01')
            ->call('save');

        $this->assertDatabaseHas('contents', [
            'title' => 'Mengelola Stress Ujian',
            'type' => 'artikel',
            'video_url' => null,
        ]);
    }

    public function test_guru_bk_can_create_video_with_embed_url(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(ContentManagement::class)
            ->call('create')
            ->set('title', 'Teknik Relaksasi')
            ->set('description', 'Video panduan relaksasi singkat.')
            ->set('type', 'video')
            ->set('video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ')
            ->set('published_at', '2026-08-01')
            ->call('save')
            ->assertHasNoErrors();

        $content = Content::where('title', 'Teknik Relaksasi')->firstOrFail();
        $this->assertSame('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $content->video_url);
        $this->assertSame('https://www.youtube.com/embed/dQw4w9WgXcQ', $content->embedUrl());
    }

    public function test_video_url_is_required_when_type_is_video(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(ContentManagement::class)
            ->call('create')
            ->set('title', 'Video Tanpa URL')
            ->set('description', 'Deskripsi.')
            ->set('type', 'video')
            ->set('video_url', '')
            ->set('published_at', '2026-08-01')
            ->call('save')
            ->assertHasErrors(['video_url']);
    }

    public function test_guru_bk_can_edit_content(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $content = Content::create([
            'title' => 'Judul Lama', 'description' => 'Isi lama.', 'type' => 'artikel',
            'author' => 'A', 'published_at' => '2026-01-01',
        ]);

        Livewire::actingAs($guruBk)
            ->test(ContentManagement::class)
            ->call('edit', $content->id)
            ->set('title', 'Judul Baru')
            ->call('save');

        $this->assertDatabaseHas('contents', ['id' => $content->id, 'title' => 'Judul Baru']);
    }

    public function test_guru_bk_can_delete_content(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);
        $content = Content::create([
            'title' => 'Judul', 'description' => 'Isi.', 'type' => 'artikel',
            'published_at' => '2026-01-01',
        ]);

        Livewire::actingAs($guruBk)
            ->test(ContentManagement::class)
            ->call('confirmDelete', $content->id)
            ->call('delete');

        $this->assertDatabaseMissing('contents', ['id' => $content->id]);
    }

    public function test_title_description_and_published_at_are_required(): void
    {
        $guruBk = User::factory()->create(['role' => UserRole::GuruBk]);

        Livewire::actingAs($guruBk)
            ->test(ContentManagement::class)
            ->call('create')
            ->set('title', '')
            ->set('description', '')
            ->set('published_at', '')
            ->call('save')
            ->assertHasErrors(['title', 'description', 'published_at']);
    }
}
