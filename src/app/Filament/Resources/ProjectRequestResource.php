<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectRequestResource\Pages;
use App\Models\ProjectRequest;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class ProjectRequestResource extends Resource
{
    protected static ?string $model = ProjectRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static ?string $navigationGroup = 'Коммуникации';

    public static function getModelLabel(): string
    {
        return 'заявка на проект';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Заявки на проект';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Имя')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('contact')
                    ->label('Контакт')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('task')
                    ->label('Задача')
                    ->rows(8)
                    ->disabled()
                    ->dehydrated(false),
                Placeholder::make('attachment')
                    ->label('Вложение')
                    ->content(function (?ProjectRequest $record): HtmlString|string {
                        if (!$record?->attachment_url) {
                            return 'Без вложения';
                        }

                        return new HtmlString(sprintf(
                            '<a href="%s" target="_blank" rel="noreferrer">%s</a>',
                            e($record->attachment_url),
                            e($record->attachment_name ?? 'Скачать файл')
                        ));
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact')
                    ->label('Контакт')
                    ->searchable(),
                IconColumn::make('attachment_path')
                    ->label('Файл')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Создана')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('attachment')
                    ->label('Файл')
                    ->icon('heroicon-o-paper-clip')
                    ->visible(fn (ProjectRequest $record) => filled($record->attachment_url))
                    ->url(fn (ProjectRequest $record) => $record->attachment_url, shouldOpenInNewTab: true),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjectRequests::route('/'),
            'view' => Pages\ViewProjectRequest::route('/{record}'),
            'edit' => Pages\EditProjectRequest::route('/{record}/edit'),
        ];
    }
}
