<?php

namespace App\Filament\Resources\ProjectRequestResource\Pages;

use App\Filament\Resources\ProjectRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProjectRequests extends ListRecords
{
    protected static string $resource = ProjectRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
