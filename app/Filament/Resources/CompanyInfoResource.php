<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyInfoResource\Pages;
use App\Models\CompanyInfo;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyInfoResource extends Resource
{
    protected static ?string $model = CompanyInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Company Information')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Website')
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Website Name')
                                    ->placeholder('AB Sign Supplies')
                                    ->maxLength(255)
                                    ->required(),
                                TextInput::make('tagline')
                                    ->label('Tagline')
                                    ->maxLength(255)
                                    ->helperText('Leave blank to hide the tagline on the public site.')
                                    ->nullable(),
                                FileUpload::make('logo_path')->label('Logo')->disk('public')->visibility('public')
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                        Tab::make('Hero')
                            ->schema([
                                TextInput::make('hero_headline')
                                    ->label('Headline')
                                    ->maxLength(255)
                                    ->nullable()
                                    ->helperText('Defaults to "Your Complete Source for Signage Supplies" when left empty.'),
                                Textarea::make('hero_subheadline')
                                    ->label('Supporting copy')
                                    ->rows(3)
                                    ->nullable(),
                                TextInput::make('hero_primary_cta_label')
                                    ->label('Primary CTA Label')
                                    ->maxLength(100)
                                    ->nullable(),
                                TextInput::make('hero_primary_cta_url')
                                    ->label('Primary CTA URL')
                                    ->helperText('Accepts relative paths (e.g. /products) or full URLs.')
                                    ->maxLength(255)
                                    ->nullable(),
                                TextInput::make('hero_secondary_cta_label')
                                    ->label('Secondary CTA Label')
                                    ->maxLength(100)
                                    ->nullable(),
                                TextInput::make('hero_secondary_cta_url')
                                    ->label('Secondary CTA URL')
                                    ->helperText('Accepts relative paths (e.g. /contact) or full URLs.')
                                    ->maxLength(255)
                                    ->nullable(),
                                Select::make('hero_media_type')
                                    ->label('Background Media Type')
                                    ->placeholder('None')
                                    ->options([
                                        'image' => 'Image',
                                        'video' => 'Video',
                                    ])
                                    ->nullable(),
                                FileUpload::make('hero_media_path')
                                    ->label('Background Media')
                                    ->disk('public')
                                    ->directory('company/hero')
                                    ->visibility('public')
                                    ->acceptedFileTypes([
                                        'image/jpeg',
                                        'image/png',
                                        'image/webp',
                                        'video/mp4',
                                        'video/webm',
                                    ])
                                    ->helperText('Upload an image or short looping video to sit behind the hero content.')
                                    ->nullable()
                                    ->visible(fn (callable $get): bool => filled($get('hero_media_type'))),
                                TextInput::make('stat_one_value')
                                    ->label('Stat 1 Value')
                                    ->maxLength(50)
                                    ->nullable(),
                                TextInput::make('stat_one_label')
                                    ->label('Stat 1 Label')
                                    ->maxLength(100)
                                    ->nullable(),
                                Select::make('stat_one_icon')
                                    ->label('Stat 1 Icon')
                                    ->options(self::statIconOptions())
                                    ->nullable(),
                                TextInput::make('stat_two_value')
                                    ->label('Stat 2 Value')
                                    ->maxLength(50)
                                    ->nullable(),
                                TextInput::make('stat_two_label')
                                    ->label('Stat 2 Label')
                                    ->maxLength(100)
                                    ->nullable(),
                                Select::make('stat_two_icon')
                                    ->label('Stat 2 Icon')
                                    ->options(self::statIconOptions())
                                    ->nullable(),
                                TextInput::make('stat_three_value')
                                    ->label('Stat 3 Value')
                                    ->maxLength(50)
                                    ->nullable(),
                                TextInput::make('stat_three_label')
                                    ->label('Stat 3 Label')
                                    ->maxLength(100)
                                    ->nullable(),
                                Select::make('stat_three_icon')
                                    ->label('Stat 3 Icon')
                                    ->options(self::statIconOptions())
                                    ->nullable(),
                            ])
                            ->columns(2),
                        Tab::make('Overview')
                            ->schema([
                                RichEditor::make('about_us')
                                    ->label('About Us')
                                    ->required()
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Contact')
                            ->schema([
                                TextInput::make('contact_email')
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('contact_phone')
                                    ->required()
                                    ->maxLength(255),
                                Textarea::make('address')
                                    ->rows(4)
                                    ->required()
                                    ->columnSpanFull(),
                                Textarea::make('google_map_embed')
                                    ->rows(4)
                                    ->label('Google Map Embed')
                                    ->helperText('Paste the iframe embed code from Google Maps.')
                                    ->nullable()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
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
                    ->circular()
                    ->height(40)
                    ->defaultImageUrl(asset('images/logo.svg')),
                TextColumn::make('site_name')
                    ->label('Website Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('tagline')
                    ->label('Tagline')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('contact_email')
                    ->label('Email')
                    ->sortable(),
                TextColumn::make('contact_phone')
                    ->label('Phone')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
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
        return [
            //
        ];
    }

    protected static function statIconOptions(): array
    {
        return [
            'box' => 'Shipping Box',
            'globe' => 'Global Reach',
            'headset' => 'Support Headset',
            'bolt' => 'Lightning Bolt',
            'certificate' => 'Certification',
            'star' => 'Star',
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyInfos::route('/'),
            'create' => Pages\CreateCompanyInfo::route('/create'),
            'edit' => Pages\EditCompanyInfo::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) CompanyInfo::query()->count();
    }

    public static function canCreate(): bool
    {
        return ! CompanyInfo::query()->exists();
    }
}






