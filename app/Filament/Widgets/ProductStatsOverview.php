<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ProductStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $totalProducts = Product::query()->count();
        $activeProducts = Product::query()->active()->count();
        $featuredProducts = Product::query()->featured(true)->count();

        $sparkline = $this->productSparkline(points: 7);

        return [
            Stat::make('Total Products', number_format($totalProducts))
                ->description('Catalog size')
                ->descriptionIcon('heroicon-o-cube')
                ->chart($sparkline)
                ->color('primary')
                ->extraAttributes(['class' => 'bg-dark text-secondary border-none shadow-sm']),
            Stat::make('Active Products', number_format($activeProducts))
                ->description('Available for sale')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('primary')
                ->chart($sparkline)
                ->extraAttributes(['class' => 'bg-dark text-secondary border-none shadow-sm']),
            Stat::make('Featured Products', number_format($featuredProducts))
                ->description('Highlighted in catalog')
                ->descriptionIcon('heroicon-o-star')
                ->color('primary')
                ->chart($sparkline)
                ->extraAttributes(['class' => 'bg-dark text-secondary border-none shadow-sm']),
        ];
    }

    /**
     * @return array<int, int>
     */
    protected function productSparkline(int $points = 14): array
    {
        return $this->countByDay(Product::query(), $points);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return array<int, int>
     */
    protected function countByDay($query, int $points): array
    {
        $startDate = Carbon::today()->subDays($points - 1);

        /** @var Collection<int, int> $counts */
        $counts = $query
            ->whereDate('created_at', '>=', $startDate)
            ->get()
            ->groupBy(fn ($model) => Carbon::parse($model->created_at)->format('Y-m-d'))
            ->map->count();

        return collect(range(0, $points - 1))
            ->map(function (int $day) use ($counts, $startDate): int {
                $date = $startDate->copy()->addDays($day)->format('Y-m-d');

                return (int) ($counts[$date] ?? 0);
            })
            ->toArray();
    }
}
