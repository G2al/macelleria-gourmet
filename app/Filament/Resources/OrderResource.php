<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmedMail;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Ordini';
    protected static ?string $pluralLabel = 'Ordini';
    protected static ?string $modelLabel = 'Ordine';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->label('Cliente')
                ->options(User::pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\DatePicker::make('pickup_date')
                ->label('Data ritiro')
                ->required(),

            Forms\Components\TimePicker::make('pickup_time')
                ->label('Ora ritiro')
                ->required(),

            Forms\Components\Select::make('status')
                ->label('Stato')
                ->options([
                    'pending' => 'In attesa',
                    'confirmed' => 'Confermato',
                    'cancelled' => 'Annullato',
                    'completed' => 'Completato',
                ])
                ->default('pending')
                ->required(),

            Forms\Components\Textarea::make('notes')
                ->label('Note')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('pickup_date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('pickup_time')
                    ->label('Ora')
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->format('H:i')),

                TextColumn::make('items_summary')
                    ->label('Prodotti')
                    ->html()
                    ->getStateUsing(function (Order $record) {
                        $items = $record->items;
                        if ($items->isEmpty()) {
                            return '<span style="color:#aaa;">Nessun prodotto</span>';
                        }

                        if ($items->count() === 1) {
                            return e($items->first()->product->name);
                        }

                        $first = e($items->first()->product->name);
                        $extra = $items->count() - 1;

                        return "{$first} <span style='color:#aaa;'>+ altri {$extra}</span>";
                    }),


                TextColumn::make('total_price')
                    ->label('Totale (€)')
                    ->money('EUR')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'cancelled',
                        'gray' => 'completed',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => '🕓 In attesa',
                        'confirmed' => '✅ Confermato',
                        'cancelled' => '❌ Annullato',
                        'completed' => '🏁 Completato',
                    }),

                TextColumn::make('created_at')
                    ->label('Creato il')
                    ->dateTime('d/m/Y H:i'),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Stato')
                    ->options([
                        'pending' => 'In attesa',
                        'confirmed' => 'Confermato',
                        'cancelled' => 'Annullato',
                        'completed' => 'Completato',
                    ]),
            ])

            ->actions([
                Action::make('conferma')
                    ->label('Conferma')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        $record->update(['status' => 'confirmed']);

                        Mail::to($record->user->email)->send(new OrderConfirmedMail($record));
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('Dettagli')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading('Riepilogo ordine')
                    ->modalDescription('Visualizza tutti i dettagli di questo ordine.')
                    ->modalWidth('5xl') 
                    ->modalContent(fn (Order $record) => view('filament.orders.view-modal', ['order' => $record])),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])

            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
