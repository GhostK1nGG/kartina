<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Коммуникации';

    public static function getModelLabel(): string
    {
        return 'заказ';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Заказы';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status')
                    ->label('Статус')
                    ->options(Order::STATUSES)
                    ->required(),
                TextInput::make('customer_name')
                    ->label('Имя')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('contact')
                    ->label('Контакт')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('phone')
                    ->label('Телефон')
                    ->disabled()
                    ->dehydrated(false),
                TextInput::make('address')
                    ->label('Адрес')
                    ->disabled()
                    ->dehydrated(false),
                Textarea::make('comment')
                    ->label('Комментарий')
                    ->rows(3)
                    ->disabled()
                    ->dehydrated(false),
                Placeholder::make('totals')
                    ->label('Сумма')
                    ->content(fn (Order $record): string => sprintf(
                        '%s ₽ / %s $',
                        number_format((float) $record->total_rub, 0, ',', ' '),
                        number_format((float) $record->total_usd, 0, '.', ' ')
                    )),
                Textarea::make('cart_snapshot')
                    ->label('Снимок корзины')
                    ->rows(18)
                    ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
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
                TextColumn::make('customer_name')
                    ->label('Имя')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('contact')
                    ->label('Контакт')
                    ->searchable(),
                TextColumn::make('total_rub')
                    ->label('Сумма, ₽')
                    ->money('RUB', divideBy: 1)
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Статус')
                    ->formatStateUsing(fn (string $state) => Order::STATUSES[$state] ?? $state)
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Создан')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Статус')
                    ->options(Order::STATUSES),
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

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
