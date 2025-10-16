<?php

namespace App\Filament\Resources\CompanyInfoResource\Pages;

use App\Filament\Resources\CompanyInfoResource;
use App\Models\CompanyInfo;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyInfos extends ListRecords
{
    protected static string $resource = CompanyInfoResource::class;

    protected function getHeaderActions(): array
    {
        if (CompanyInfo::query()->exists()) {
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Create Company Profile'),
        ];
    }
}
