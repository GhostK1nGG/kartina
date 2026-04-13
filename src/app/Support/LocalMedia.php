<?php

namespace App\Support;

class LocalMedia
{
    public static function resolvePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalizedPath = ltrim($path, '/\\');

        foreach (array_unique([
            storage_path('app/public/' . $normalizedPath),
            public_path($normalizedPath),
        ]) as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    public static function isImage(?string $path): bool
    {
        $resolvedPath = static::resolvePath($path);

        if (!$resolvedPath) {
            return false;
        }

        $mimeType = mime_content_type($resolvedPath);

        return is_string($mimeType) && str_starts_with($mimeType, 'image/');
    }
}
