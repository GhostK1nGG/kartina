<?php

namespace App\Filament\Resources\PaintingResource\RelationManagers;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleryRelationManager extends RelationManager
{
    protected static string $relationship = 'gallery';

    protected static ?string $title = 'Галерея';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image_path')
                    ->label('Фото')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('paintings/gallery')
                    ->visibility('public')
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Порядок')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Фото')
                    ->square(),
                TextColumn::make('sort_order')
                    ->label('Порядок')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Добавлено')
                    ->dateTime('d.m.Y H:i'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('uploadImages')
                    ->label('Добавить фото')
                    ->icon('heroicon-o-photo')
                    ->form([
                        FileUpload::make('images')
                            ->label('Фотографии галереи')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('paintings/gallery')
                            ->visibility('public')
                            ->required()
                            ->helperText('Дополнительные фото. Главное фото задаётся отдельно в карточке картины.'),
                    ])
                    ->action(function (array $data): void {
                        $ownerRecord = $this->getOwnerRecord();
                        $images = array_values(array_filter($data['images'] ?? []));
                        $existingCount = $ownerRecord->gallery()->count();
                        $remainingSlots = max(5 - $existingCount, 0);

                        if ($remainingSlots === 0) {
                            Notification::make()
                                ->title('Лимит галереи достигнут')
                                ->body('У этой картины уже есть 5 дополнительных фото.')
                                ->danger()
                                ->send();

                            return;
                        }

                        if (count($images) > $remainingSlots) {
                            Notification::make()
                                ->title('Слишком много файлов')
                                ->body("Сейчас можно добавить только {$remainingSlots} фото.")
                                ->danger()
                                ->send();

                            return;
                        }

                        $nextSortOrder = ((int) ($ownerRecord->gallery()->max('sort_order') ?? -1)) + 1;

                        foreach ($images as $imagePath) {
                            $ownerRecord->gallery()->create([
                                'image_path' => $imagePath,
                                'sort_order' => $nextSortOrder,
                            ]);

                            $nextSortOrder++;
                        }

                        Notification::make()
                            ->title('Фото галереи добавлены')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
