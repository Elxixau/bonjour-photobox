<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?string $navigationLabel = 'Data order ';
    
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
              ->schema([
                TextInput::make('order_code')
                    ->required()
                    ->unique(Order::class, 'order_code', ignoreRecord: true),
                TextInput::make('email')
                    ->email()
                    ->required(),
                Select::make('kategori_id')
                    ->label('Kategori')
                    ->options(
                        \App\Models\Kategori::query()
                            ->pluck('nama', 'id')
                            ->filter(fn ($value) => !is_null($value))
                    )
                    ->required(),
                Select::make('qr_id')
    ->label('QR Access')
    ->options(
        \App\Models\QrAccess::all()
            ->mapWithKeys(fn ($qr) => [$qr->id => $qr->img_path ?? 'Unknown'])
    )
    ->nullable(),

                TextInput::make('harga_paket')
                    ->numeric()
                    ->required(),
                TextInput::make('total_harga')
                    ->numeric()
                    ->required(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                   TextColumn::make('order_code')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('kategori.nama')->label('Kategori')->sortable(),
                TextColumn::make('harga_paket')->money('idr'),
                TextColumn::make('total_harga')->money('idr'),
                TextColumn::make('status')->badge()->color(fn (string $state): string => match ($state) {
                'success' => 'success', 
                'pending'  => 'warning',    
            }),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            //
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
