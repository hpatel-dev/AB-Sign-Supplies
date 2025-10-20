<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeoEntryResource\Pages;
use App\Models\SeoEntry;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeoEntryResource extends Resource
{
    protected static ?string $model = SeoEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationGroup = 'Marketing';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('SEO')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Overview')
                            ->schema([
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->helperText('Match this to the Nuxt route identifier, e.g. "home" or "about".')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true),
                                TextInput::make('title')
                                    ->label('Page Title')
                                    ->maxLength(255)
                                    ->nullable(),
                                Textarea::make('description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->nullable(),
                                TextInput::make('canonical_url')
                                    ->label('Canonical URL')
                                    ->placeholder('https://example.com/about')
                                    ->url()
                                    ->maxLength(2048)
                                    ->nullable(),
                            ])
                            ->columns(2),
                        Tab::make('Open Graph')
                            ->schema([
                                TextInput::make('og_title')
                                    ->label('OG Title')
                                    ->maxLength(255)
                                    ->nullable(),
                                Textarea::make('og_description')
                                    ->label('OG Description')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->nullable(),
                                FileUpload::make('og_image_path')
                                    ->label('OG Image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('seo/og')
                                    ->visibility('public')
                                    ->imagePreviewHeight('200')
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Twitter')
                            ->schema([
                                TextInput::make('twitter_title')
                                    ->label('Twitter Title')
                                    ->maxLength(255)
                                    ->nullable(),
                                Textarea::make('twitter_description')
                                    ->label('Twitter Description')
                                    ->rows(3)
                                    ->maxLength(500)
                                    ->nullable(),
                                FileUpload::make('twitter_image_path')
                                    ->label('Twitter Image')
                                    ->image()
                                    ->imageEditor()
                                    ->directory('seo/twitter')
                                    ->visibility('public')
                                    ->imagePreviewHeight('200')
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Additional Meta')
                            ->schema([
                                Repeater::make('extra_meta')
                                    ->label('Custom Meta Tags')
                                    ->schema([
                                        Select::make('attribute')
                                            ->label('Attribute')
                                            ->options([
                                                'name' => 'name',
                                                'property' => 'property',
                                                'http_equiv' => 'http-equiv',
                                            ])
                                            ->default('name')
                                            ->required(),
                                        TextInput::make('attribute_value')
                                            ->label('Attribute Value')
                                            ->maxLength(255)
                                            ->required(),
                                        TextInput::make('content')
                                            ->label('Content')
                                            ->maxLength(1024)
                                            ->required(),
                                    ])
                                    ->columns(3)
                                    ->default([])
                                    ->afterStateHydrated(function (Repeater $component, ?array $state): void {
                                        if (! $state) {
                                            return;
                                        }

                                        $component->state(collect($state)->map(function (array $item): array {
                                            $attribute = 'name';
                                            $value = isset($item['name']) ? (string) $item['name'] : null;

                                            if (isset($item['property'])) {
                                                $attribute = 'property';
                                                $value = (string) $item['property'];
                                            } elseif (isset($item['http_equiv'])) {
                                                $attribute = 'http_equiv';
                                                $value = (string) $item['http_equiv'];
                                            }

                                            return [
                                                'attribute' => $attribute,
                                                'attribute_value' => $value,
                                                'content' => isset($item['content']) ? (string) $item['content'] : '',
                                            ];
                                        })->all());
                                    })
                                    ->mutateDehydratedStateUsing(function (?array $state): array {
                                        if (! $state) {
                                            return [];
                                        }

                                        return collect($state)->map(function (array $item): array {
                                            $attribute = $item['attribute'] ?? 'name';
                                            $value = isset($item['attribute_value']) ? trim((string) $item['attribute_value']) : null;
                                            $content = isset($item['content']) ? trim((string) $item['content']) : '';

                                            return array_filter([
                                                $attribute === 'http_equiv' ? 'http_equiv' : $attribute => $value,
                                                'content' => $content,
                                            ], static fn ($value) => $value !== null && $value !== '');
                                        })->filter()->values()->all();
                                    })
                                    ->helperText('Add bespoke meta tags (e.g. robots or author). Leave blank if unused.'),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Title')
                    ->limit(50)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('canonical_url')
                    ->label('Canonical')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
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
            'index' => Pages\ListSeoEntries::route('/'),
            'create' => Pages\CreateSeoEntry::route('/create'),
            'edit' => Pages\EditSeoEntry::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) SeoEntry::query()->count();
    }
}
