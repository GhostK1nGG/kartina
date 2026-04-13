<?php

namespace App\Filament\Resources\PaintingResource\Pages;

use App\Filament\Resources\PaintingResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreatePainting extends CreateRecord
{
    protected static string $resource = PaintingResource::class;

    protected array $galleryUploads = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->galleryUploads = array_values(array_filter($data['gallery_uploads'] ?? []));
        unset($data['gallery_uploads']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->galleryUploads === []) {
            return;
        }

        $imagesToPersist = array_slice($this->galleryUploads, 0, 5);
        $imagesToDelete = array_slice($this->galleryUploads, 5);

        foreach ($imagesToPersist as $index => $imagePath) {
            $this->record->gallery()->create([
                'image_path' => $imagePath,
                'sort_order' => $index,
            ]);
        }

        foreach ($imagesToDelete as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        if ($imagesToDelete !== []) {
            Notification::make()
                ->title('Часть файлов не добавлена')
                ->body('Для одной картины доступно только 5 дополнительных фото.')
                ->warning()
                ->send();
        }
    }
}
