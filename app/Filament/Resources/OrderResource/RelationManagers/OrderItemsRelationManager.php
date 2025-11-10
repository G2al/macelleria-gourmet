<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';
    protected static ?string $title = 'Prodotti dell’ordine';
    protected static ?string $recordTitleAttribute = 'product_id';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Prodotto')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('weight')
                    ->label('Peso (Kg)')
                    ->formatStateUsing(fn ($state) => number_format($state, 3, ',', '.') . ' kg'),


                TextColumn::make('price_per_kg')
                    ->label('Prezzo €/Kg')
                    ->money('EUR'),

                TextColumn::make('total_price')
                    ->label('Totale')
                    ->money('EUR')
                    ->color('success')
                    ->weight('bold'),
            ])
            ->filters([])
            ->headerActions([]) // disattiva creazione diretta
            ->actions([])       // disattiva modifica/elimina
            ->bulkActions([]);
    }
}
