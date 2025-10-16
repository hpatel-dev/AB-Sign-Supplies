<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Support\RawJs;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Carbon;

class ProductsTrendChart extends LineChartWidget
{
    protected static ?string $heading = 'Products Created (Last 30 Days)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $start = Carbon::today()->subDays(29);
        $labels = [];
        $data = [];

        $counts = Product::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
            ->whereDate('created_at', '>=', $start)
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('aggregate', 'date');

        for ($i = 0; $i < 30; $i++) {
            $date = $start->copy()->addDays($i);
            $label = $date->format('M d');
            $labels[] = $label;
            $data[] = (int) ($counts[$date->format('Y-m-d')] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Products',
                    'data' => $data,
                    'tension' => 0.4,
                    'borderColor' => '#FE0002',
                    'backgroundColor' => 'rgba(254,0,2,0.2)',
                    'fill' => 'start',
                    'pointBackgroundColor' => '#FE0002',
                    'pointHoverBackgroundColor' => '#FFFFFF',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => '#FFFFFF',
                    ],
                ],
                'y' => [
                    'grid' => [
                        'color' => RawJs::make("'rgba(255,255,255,0.1)'"),
                    ],
                    'ticks' => [
                        'color' => '#FFFFFF',
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    protected function getContentHeight(): ?string
    {
        return '320px';
    }

    protected function getExtraCardAttributes(): array
    {
        return [
            'class' => 'bg-dark text-secondary border-none shadow-sm',
        ];
    }
}
