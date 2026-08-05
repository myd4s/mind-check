<?php

namespace Tests\Feature\Siswa;

use App\Enums\UserRole;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_siswa_can_view_article_detail(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);
        $content = Content::create([
            'title' => 'Mengelola Stress Ujian', 'description' => 'Tips lengkap.', 'type' => 'artikel',
            'author' => 'Guru BK', 'published_at' => '2026-01-01',
        ]);

        $this->actingAs($siswa)
            ->get(route('siswa.content-detail', $content))
            ->assertOk()
            ->assertSee('Mengelola Stress Ujian');
    }

    public function test_video_detail_embeds_youtube_iframe(): void
    {
        $siswa = User::factory()->create(['role' => UserRole::Siswa]);
        $content = Content::create([
            'title' => 'Teknik Relaksasi', 'description' => 'Video.', 'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'published_at' => '2026-01-01',
        ]);

        $this->actingAs($siswa)
            ->get(route('siswa.content-detail', $content))
            ->assertOk()
            ->assertSee('https://www.youtube.com/embed/dQw4w9WgXcQ', false);
    }
}
