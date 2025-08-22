<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KategoriResource\Pages;
use App\Filament\Resources\KategoriResource\RelationManagers;
use App\Models\Addon;
use App\Models\Kategori;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    
    protected static ?string $navigationGroup = 'Manajemen Sistem';
    
    protected static ?string $navigationLabel = 'Paket Photobox';
    
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                   Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('harga')
                    ->numeric()
                    ->required()                    
                    ->placeholder('Contoh: 30000'),
                Forms\Components\TextInput::make('waktu')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Contoh: 2'),
                Forms\Components\TextInput::make('jumlah_cetak')
                    ->required()
                    ->maxLength(100)
                    ->placeholder('Contoh: 1'),
                Repeater::make('addons')
                ->relationship()
                ->label('Addons')
                ->schema([
                    TextInput::make('nama')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('harga')
                        ->required()
                        ->numeric()
                        ->placeholder('Contoh: 5000'),
                ])
                ->columns(2)
                ->createItemButtonLabel('Tambah Addons'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('harga')->sortable(),
                Tables\Columns\TextColumn::make('waktu'),
                Tables\Columns\TextColumn::make('jumlah_cetak'),
                Tables\Columns\TextColumn::make('addons_count')
                    ->counts('addons')
                    ->label('Jumlah Addons'),
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
            'index' => Pages\ListKategoris::route('/'),
            'create' => Pages\CreateKategori::route('/create'),
            'edit' => Pages\EditKategori::route('/{record}/edit'),
        ];
    }
}
