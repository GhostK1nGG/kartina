<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Painting extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'year',
        'size',
        'price_rub',
        'price_usd',
        'short_desc',
        'full_desc',
        'main_image',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'price_rub' => 'decimal:2',
        'price_usd' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $painting): void {
            if (blank($painting->slug) && filled($painting->title)) {
                $painting->slug = static::generateUniqueSlug($painting->title, $painting->getKey());
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return $this->newQuery()
            ->with(['category', 'gallery'])
            ->where($field ?? $this->getRouteKeyName(), $value)
            ->active()
            ->firstOrFail();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(PaintingGallery::class)->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getMainImageUrlAttribute(): ?string
    {
        if (!$this->main_image) {
            return null;
        }

        if (str_starts_with($this->main_image, 'assets/')) {
            return asset($this->main_image);
        }

        if (str_starts_with($this->main_image, 'storage/')) {
            return asset($this->main_image);
        }

        return Storage::url($this->main_image);
    }

    protected static function generateUniqueSlug(string $title, mixed $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slugBase = $baseSlug !== '' ? $baseSlug : 'painting';
        $slug = $slugBase;
        $suffix = 1;

        while (
            static::query()
                ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
