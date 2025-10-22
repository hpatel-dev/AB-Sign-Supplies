<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make('Company')
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('slug')
                                    ->unique(ignoreRecord: true)
                                    ->helperText('Defaults to a slug of the company name.')
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('tagline')
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('summary')
                                    ->maxLength(1024)
                                    ->nullable(),
                                FileUpload::make('logo_path')
                                    ->label('Logo')
                                    ->image()
                                    ->imagePreviewHeight('160')
                                    ->directory('company/logos')
                                    ->visibility('public')
                                    ->nullable(),
                                RichEditor::make('overview')
                                    ->columnSpanFull()
                                    ->nullable()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'bulletList',
                                        'undo',
                                        'redo',
                                    ]),
                            ])
                            ->columns(2),
                        Section::make('Contact')
                            ->schema([
                                TextInput::make('contact_email')
                                    ->email()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('contact_phone')
                                    ->tel()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('address')
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('website')
                                    ->url()
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(2),
                    ]),
                Section::make('Services')
                    ->schema([
                        Repeater::make('services')
                            ->relationship()
                            ->schema([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('description')
                                    ->maxLength(1024)
                                    ->nullable(),
                                TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->orderable('sort_order')
                            ->minItems(0)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->disk('public')
                    ->defaultImageUrl(asset('images/logo.svg'))
                    ->circular()
                    ->height(40),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tagline')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('contact_email')
                    ->label('Email')
                    ->toggleable(),
                TextColumn::make('contact_phone')
                    ->label('Phone')
                    ->toggleable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([])
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}

