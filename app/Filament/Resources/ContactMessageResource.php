<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationLabel = 'Leads';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Lead Details')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('name')
                                    ->disabled()
                                    ->label('Name'),
                                TextInput::make('email')
                                    ->disabled()
                                    ->label('Email'),
                                TextInput::make('phone')
                                    ->disabled()
                                    ->label('Phone')
                                    ->columnSpan(1),
                                TextInput::make('product')
                                    ->disabled()
                                    ->label('Product')
                                    ->columnSpan(1),
                            ])
                            ->columns(2),
                        Textarea::make('message')
                            ->disabled()
                            ->rows(6)
                            ->label('Message'),
                    ])
                    ->columns(1),
                Section::make('Status')
                    ->schema([
                        DateTimePicker::make('handled_at')
                            ->label('Handled at')
                            ->seconds(false)
                            ->nullable()
                            ->helperText('Set when the lead has been handled.'),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->sortable()
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Email copied')
                    ->copyMessageDuration(1500),
                TextColumn::make('message')
                    ->label('Message')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('product')
                    ->label('Product')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->limit(24)
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('handled_at')
                    ->label('Handled')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('handled')
                    ->label('Handled')
                    ->boolean()
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('handled_at'),
                        false: fn (Builder $query) => $query->whereNull('handled_at'),
                        blank: fn (Builder $query) => $query
                    ),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->label('Update'),
                Tables\Actions\Action::make('markHandled')
                    ->label('Mark handled')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->visible(fn (ContactMessage $record): bool => blank($record->handled_at))
                    ->action(fn (ContactMessage $record) => $record->update(['handled_at' => now()])),
                Tables\Actions\Action::make('markOpen')
                    ->label('Mark open')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->requiresConfirmation()
                    ->color('secondary')
                    ->visible(fn (ContactMessage $record): bool => filled($record->handled_at))
                    ->action(fn (ContactMessage $record) => $record->update(['handled_at' => null])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view' => Pages\ViewContactMessage::route('/{record}'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}

