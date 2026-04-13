<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PaintingGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'painting_id',
        'image_path',
        'sort_order',
    ];

    public function painting(): BelongsTo
    {
        return $this->belongsTo(Painting::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        if (str_starts_with($this->image_path, 'assets/')) {
            return asset($this->image_path);
        }

        if (str_starts_with($this->image_path, 'storage/')) {
            return asset($this->image_path);
        }

        return Storage::url($this->image_path);
    }
}
