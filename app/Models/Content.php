<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'video_url',
        'author',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'date',
    ];

    /**
     * Konversi URL YouTube (watch/share/short) ke URL embed agar bisa
     * ditampilkan dalam <iframe>. URL video lain (mis. Vimeo, direct embed
     * link) dikembalikan apa adanya karena sudah umumnya embeddable.
     */
    public function embedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/watch\?v=|youtube\.com\/shorts\/)([\w-]+)/', $this->video_url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return $this->video_url;
    }
}
