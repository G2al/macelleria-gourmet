<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Prodotti';
    protected static ?string $pluralLabel = 'Prodotti';
    protected static ?string $modelLabel = 'Prodotto';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nome prodotto')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('description')
                    ->label('Descrizione')
                    ->rows(3),

                FileUpload::make('photo')
                    ->label('Foto')
                    ->image()
                    ->directory('products')
                    ->disk('public')
                    ->imagePreviewHeight('100')
                    ->visibility('public'),

                Forms\Components\Select::make('category_id')
                    ->label('Categoria')
                    ->options(Category::pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('purchase_type')
                    ->label('Modalità di acquisto')
                    ->options([
                        'weight'   => 'Al peso (kg)',
                        'unit'     => 'A pezzi',
                        'package'  => 'A confezioni',
                    ])
                    ->default('weight')
                    ->required()
                    ->helperText('Scegli come il cliente ordinerà questo prodotto'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Attivo')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->disk('public')
                    ->defaultImageUrl(asset('images/placeholder-product.png'))
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable(),

                Tables\Columns\TextColumn::make('purchase_type')
                    ->label('Modalità')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'weight'   => 'Peso',
                        'unit'     => 'Pezzi',
                        'package'  => 'Confezioni',
                        default    => $state,
                    })
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Attivo')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creazione')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship('category', 'name'),

                Tables\Filters\SelectFilter::make('purchase_type')
                    ->label('Modalità acquisto')
                    ->options([
                        'weight'   => 'Peso',
                        'unit'     => 'Pezzi',
                        'package'  => 'Confezioni',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Attivo'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}