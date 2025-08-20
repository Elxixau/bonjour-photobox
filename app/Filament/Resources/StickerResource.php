<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StickerResource\Pages;
use App\Filament\Resources\StickerResource\RelationManagers;
use App\Models\Sticker;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StickerResource extends Resource
{
    protected static ?string $model = Sticker::class;

    protected static ?string $navigationGroup = 'Manajemen Asset';

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('Nama Sticker'),
                Forms\Components\FileUpload::make('img_path')
                    ->label('Gambar Sticker')
                    ->image()
                    ->directory('stickers')
                    ->required(),
                Forms\Components\Toggle::make('active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\ImageColumn::make('img_path')->label('Preview'),
            ])
            ->filters([
         
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStickers::route('/'),
            'create' => Pages\CreateSticker::route('/create'),
            'edit' => Pages\EditSticker::route('/{record}/edit'),
        ];
    }
}
