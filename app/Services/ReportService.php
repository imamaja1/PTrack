<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Map a Tailwind CSS color class to a hex color string for Chart.js.
     */
    private function tailwindToHex(string $colorClass): string
    {
        $map = [
            'red'     => '#ef4444',
            'blue'    => '#3b82f6',
            'emerald' => '#10b981',
            'indigo'  => '#6366f1',
            'purple'  => '#a855f7',
            'orange'  => '#f97316',
            'yellow'  => '#eab308',
            'pink'    => '#ec4899',
            'rose'    => '#f43f5e',
            'amber'   => '#f59e0b',
            'fuchsia' => '#d946ef',
            'teal'    => '#14b8a6',
            'cyan'    => '#06b6d4',
            'lime'    => '#84cc16',
            'green'   => '#22c55e',
            'sky'     => '#0ea5e9',
            'violet'  => '#8b5cf6',
        ];

        foreach ($map as $name => $hex) {
            if (str_contains($colorClass, $name)) {
                return $hex;
            }
        }

        return '#9ca3af'; // default gray
    }

    /**
     * Fetch chart data (by category) for a given month.
     *
     * Returns structured array ready for Chart.js consumption.
     */
    public function getChartData(int $userId, string $month): array
    {
        $startDate = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $endDate   = $startDate->copy()->endOfMonth();

        return [
            'expense' => $this->buildChartDataset($userId, 'expense', $startDate, $endDate),
            'income'  => $this->buildChartDataset($userId, 'income', $startDate, $endDate),
        ];
    }

    /**
     * Build a chart dataset (labels, data, colors) for one transaction type.
     *
     * @param string $type 'income' or 'expense'
     */
    private function buildChartDataset(int $userId, string $type, Carbon $startDate, Carbon $endDate): array
    {
        $transactions = Transaction::where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();

        $labels  = [];
        $data    = [];
        $colors  = [];

        foreach ($transactions as $tx) {
            $cat      = Category::find($tx->category_id);
            $labels[] = $cat ? $cat->name : 'Lain-lain';
            $data[]   = $tx->total;
            $colors[] = ($cat && $cat->color) ? $this->tailwindToHex($cat->color) : '#9ca3af';
        }

        $hasData = !empty($data);

        return [
            'labels'  => $hasData ? $labels : ['Tidak ada data'],
            'data'    => $hasData ? $data   : [1],
            'colors'  => $hasData ? $colors : ['#e5e7eb'],
            'hasData' => $hasData,
        ];
    }
}
