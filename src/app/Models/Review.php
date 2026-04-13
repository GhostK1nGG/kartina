<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_name',
        'author_city',
        'text',
        'rating',
        'image_path',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
