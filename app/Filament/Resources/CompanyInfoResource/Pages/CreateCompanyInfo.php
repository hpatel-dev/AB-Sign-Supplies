<?php

namespace App\Filament\Resources\CompanyInfoResource\Pages;

use App\Filament\Resources\CompanyInfoResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanyInfo extends CreateRecord
{
    protected static string $resource = CompanyInfoResource::class;

    protected function authorizeAccess(): void
    {
        abort_if(! static::getResource()::canCreate(), 404);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Company info created')
            ->body('Your company profile has been published.');
    }
}
