<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaintingResource\Pages;
use App\Filament\Resources\PaintingResource\RelationManagers\GalleryRelationManager;
use App\Models\Painting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PaintingResource extends Resource
{
    protected static ?string $model = Painting::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Каталог';

    public static function getModelLabel(): string
    {
        return 'картина';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Картины';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Основное')
                    ->columns(2)
                    ->schema([
                        Select::make('category_id')
                            ->label('Категория')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('title')
                            ->label('Название')
                            ->required()
                            ->maxLength(200)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (?string $state, callable $set) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(200)
                            ->unique(ignoreRecord: true),
                        TextInput::make('year')
                            ->label('Год')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue((int) date('Y') + 1),
                        TextInput::make('size')
                            ->label('Размер')
                            ->maxLength(100),
                        TextInput::make('price_rub')
                            ->label('Цена, ₽')
                            ->numeric()
                            ->prefix('₽'),
                        TextInput::make('price_usd')
                            ->label('Цена, $')
                            ->numeric()
                            ->prefix('$'),
                        Toggle::make('is_active')
                            ->label('Показывать на сайте')
                            ->default(true),
                        Toggle::make('is_featured')
                            ->label('Показывать на главной')
                            ->default(false)
                            ->helperText('На главной странице можно отметить не больше 5 картин.')
                            ->rules([
                                fn (?Painting $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($record) {
                                    if (!$value) {
                                        return;
                                    }

                                    $featuredCount = Painting::query()
                                        ->where('is_featured', true)
                                        ->when($record, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
                                        ->count();

                                    if ($featuredCount >= 5) {
                                        $fail('На главной уже выбрано 5 картин. Сними отметку с одной из текущих, чтобы выбрать новую.');
                                    }
                                },
                            ]),
                    ]),
                Section::make('Описание и фотографии')
                    ->schema([
                        Textarea::make('short_desc')
                            ->label('Короткое описание')
                            ->rows(3)
                            ->maxLength(1000),
                        Textarea::make('full_desc')
                            ->label('Полное описание')
                            ->rows(8),
                        FileUpload::make('main_image')
                            ->label('Главное фото')
                            ->helperText('Это фото используется в каталоге, на главной странице и показывается первым на странице картины.')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('paintings')
                            ->visibility('public'),
                        FileUpload::make('gallery_uploads')
                            ->label('Дополнительные фото галереи')
                            ->helperText('Можно добавить до 5 дополнительных фотографий. Они появятся на странице картины под главным фото.')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('paintings/gallery')
                            ->visibility('public'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('main_image_url')
                    ->label('Главное фото')
                    ->square(),
                TextColumn::make('title')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('category.name')
                    ->label('Категория')
                    ->sortable(),
                TextColumn::make('gallery_count')
                    ->counts('gallery')
                    ->label('Фото в галерее')
                    ->badge(),
                TextColumn::make('price_rub')
                    ->label('Цена, ₽')
                    ->money('RUB', divideBy: 1)
                    ->sortable(),
                TextColumn::make('year')
                    ->label('Год')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('На сайте')
                    ->boolean(),
                IconColumn::make('is_featured')
                    ->label('На главной')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Категория')
                    ->relationship('category', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Показывать на сайте'),
                TernaryFilter::make('is_featured')
                    ->label('Показывать на главной'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            GalleryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaintings::route('/'),
            'create' => Pages\CreatePainting::route('/create'),
            'view' => Pages\ViewPainting::route('/{record}'),
            'edit' => Pages\EditPainting::route('/{record}/edit'),
        ];
    }
}
