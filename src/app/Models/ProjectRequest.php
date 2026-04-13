<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact',
        'task',
        'attachment_path',
    ];

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? Storage::url($this->attachment_path) : null;
    }

    public function getAttachmentNameAttribute(): ?string
    {
        return $this->attachment_path ? basename($this->attachment_path) : null;
    }
}
