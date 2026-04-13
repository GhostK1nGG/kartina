<?php

namespace App\Filament\Resources\PaintingResource\Pages;

use App\Filament\Resources\PaintingResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditPainting extends EditRecord
{
    protected static string $resource = PaintingResource::class;

    protected array $galleryUploads = [];

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->galleryUploads = array_values(array_filter($data['gallery_uploads'] ?? []));
        unset($data['gallery_uploads']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->galleryUploads === []) {
            return;
        }

        $existingCount = $this->record->gallery()->count();
        $remainingSlots = max(5 - $existingCount, 0);

        if ($remainingSlots === 0) {
            foreach ($this->galleryUploads as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            Notification::make()
                ->title('Лимит галереи достигнут')
                ->body('У этой картины уже есть 5 дополнительных фото. Удали одно из них, чтобы добавить новое.')
                ->danger()
                ->send();

            return;
        }

        $imagesToPersist = array_slice($this->galleryUploads, 0, $remainingSlots);
        $imagesToDelete = array_slice($this->galleryUploads, $remainingSlots);
        $nextSortOrder = ((int) ($this->record->gallery()->max('sort_order') ?? -1)) + 1;

        foreach ($imagesToPersist as $imagePath) {
            $this->record->gallery()->create([
                'image_path' => $imagePath,
                'sort_order' => $nextSortOrder,
            ]);

            $nextSortOrder++;
        }

        foreach ($imagesToDelete as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        if ($imagesToDelete !== []) {
            Notification::make()
                ->title('Часть файлов не добавлена')
                ->body("Сейчас можно добавить только {$remainingSlots} фото.")
                ->warning()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
