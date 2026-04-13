<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Коммуникации';

    public static function getModelLabel(): string
    {
        return 'отзыв';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Отзывы';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('author_name')
                    ->label('Автор')
                    ->required()
                    ->maxLength(100),
                TextInput::make('author_city')
                    ->label('Город')
                    ->maxLength(100),
                Select::make('rating')
                    ->label('Оценка')
                    ->options([
                        1 => '1',
                        2 => '2',
                        3 => '3',
                        4 => '4',
                        5 => '5',
                    ])
                    ->required(),
                Textarea::make('text')
                    ->label('Текст')
                    ->rows(8)
                    ->required(),
                FileUpload::make('image_path')
                    ->label('Фото')
                    ->image()
                    ->disk('public')
                    ->directory('reviews')
                    ->visibility('public'),
                Toggle::make('is_published')
                    ->label('Опубликован')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Фото')
                    ->disk('public')
                    ->square(),
                TextColumn::make('author_name')
                    ->label('Автор')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('author_city')
                    ->label('Город')
                    ->searchable(),
                TextColumn::make('rating')
                    ->label('Оценка')
                    ->badge(),
                ToggleColumn::make('is_published')
                    ->label('Опубликован'),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_published')
                    ->label('Статус публикации'),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->label('Опубликовать')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Review $record) => !$record->is_published)
                    ->action(fn (Review $record) => $record->update(['is_published' => true])),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'view' => Pages\ViewReview::route('/{record}'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}
