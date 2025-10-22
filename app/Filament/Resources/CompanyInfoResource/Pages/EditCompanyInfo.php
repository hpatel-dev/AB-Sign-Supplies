<?php

namespace App\Filament\Resources\CompanyInfoResource\Pages;

use App\Filament\Resources\CompanyInfoResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCompanyInfo extends EditRecord
{
    protected static string $resource = CompanyInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Company info updated')
            ->body('Your public company profile changes have been saved.');
    }
}
