<?php

namespace App\Models;

use App\Enums\Gender;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'nisn',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classHistories(): HasMany
    {
        return $this->hasMany(StudentClassHistory::class);
    }

    public function currentClassHistory(): HasOne
    {
        // Bukan latestOfMany(): subquery MAX(id) itu dihitung sebelum filter
        // tahun aktif diterapkan, jadi kalau baris ID tertinggi (mis. hasil
        // kenaikan kelas ke tahun depan) bukan tahun yang aktif, seluruh query
        // mengembalikan null walau ada baris valid yang lebih lama. orderBy +
        // first() (default HasOne) menerapkan filter dulu, baru ambil terbaru.
        return $this->hasOne(StudentClassHistory::class)
            ->whereHas('academicYear', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('id');
    }
}
