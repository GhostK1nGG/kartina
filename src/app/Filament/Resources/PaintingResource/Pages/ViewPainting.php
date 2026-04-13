<?php

namespace App\Filament\Resources\PaintingResource\Pages;

use App\Filament\Resources\PaintingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPainting extends ViewRecord
{
    protected static string $resource = PaintingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
